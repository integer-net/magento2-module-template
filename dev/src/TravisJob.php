<?php
declare(strict_types=1);

namespace IntegerNet\ModuleTemplate;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use function array_map as map;

/**
 * Renders YAML for travis job configuration based on supported Magento versions
 */
class TravisJob
{
    /**
     * Latest Magento version. If this version is supported, jobs for the dev branch is added
     */
    private const LATEST_MAGENTO_VERSION = '2.4';
    /**
     * Job configuration for dev branch
     */
    private const DEV_BRANCH_JOBS = [
        [
            'php' => '7.4',
            'env' => [
                'MAGENTO_VERSION=2.4-develop',
                'TEST_SUITE=integration',
            ],
        ],
    ];

    public static function getConfiguration(array $magentoVersions)
    {
        $versionConstraints = Config::getMagentoVersions();
        $jobs = [];
        foreach ($magentoVersions as $magentoKey => $magentoVersion) {
            $phpVersions = map(
                fn($constraint) => ltrim($constraint, '^~'),
                $versionConstraints[$magentoVersion]['php']
            );
            // Integration tests for each PHP version
            foreach ($phpVersions as $phpKey => $phpVersion) {
                $job = [
                    'php' => $phpVersion,
                    'env' => [
                        'MAGENTO_VERSION=' . $magentoVersion,
                        'TEST_SUITE=integration',
                    ],
                ];
                // Integration test coverage only for latest PHP and Magento version
                if ($magentoKey === array_key_last($magentoVersions) && $phpKey === array_key_last($phpVersions)) {
                    $job['env'][] = 'COVERAGE=true';
                }
                $jobs['include'][] = $job;
            }
            // Add unit tests only for latest PHP and Magento version
            if ($magentoKey === array_key_last($magentoVersions)) {
                $jobs['include'][] = [
                    'php' => $phpVersion,
                    'env' => [
                        'MAGENTO_VERSION=' . $magentoVersion,
                        'TEST_SUITE=unit',
                        'COVERAGE=true',
                    ],
                ];
            }
        }
        // Add jobs for dev branch if latest Magento version is supported
        if (in_array(self::LATEST_MAGENTO_VERSION, $magentoVersions, true)) {
            foreach (self::DEV_BRANCH_JOBS as $job) {
                $jobs['include'][] = $job;
                $jobs['allow_failures'][] = $job;
            }
        }
        return str_replace("\t", '  ', Neon::encode(['jobs' => $jobs], Encoder::BLOCK));
    }
}