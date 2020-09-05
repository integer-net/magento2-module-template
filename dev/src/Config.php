<?php

namespace IntegerNet\ModuleTemplate;

use function array_merge as merge;

/**
 * These functions contain configuration for the init script
 */
class Config
{
    /**
     * @return string[] relative paths for files where placeholders will be replaced
     */
    public static function getFilesToUpdate(): array
    {
        return [
            'composer.json',
            '.travis.yml',
            'LICENSE',
            'README.md',
            'CONTRIBUTING.md',
            'registration.php',
            'src/ExampleClass.php',
            'src/etc/module.xml',
            'tests/integration/phpunit.xml.dist',
            'tests/integration/ExampleTest.php',
            'tests/unit/ExampleTest.php',
            'tests/src/ExampleTestDataBuilder.php',
        ];
    }

    /**
     * @return string[] Associative array with placeholders as keys and default values as values
     */
    public static function getDefaultVariables(): array
    {
        // Get author name and email from Git if possible
        $gitUserName = trim(shell_exec('git config --get user.name'));
        $gitUserEmail = trim(shell_exec('git config --get user.email'));

        // Get vendor and package from Github URI if possible (matches HTTPS and SSH)
        preg_match('#github\.com[:/]([^/]+)/(.*)\.git#', shell_exec('git remote get-url origin'), $matches);
        $vendor = $matches[1] ?? 'acme-inc';
        $package = $matches[2] ?? 'magento2-awesome-module';

        // Guess company, namespace and module name based on vendor and package
        $company = ucwords(str_replace('-', ' ', $vendor));
        $moduleNamespace = str_replace(' ', '', $company);
        $commonPrefixes = self::getCommonModulePrefixes($moduleNamespace);
        $moduleName = str_replace(merge([' '], $commonPrefixes), '', ucwords(str_replace(['-', '_'], ' ', $package)));

        return [
            ':vendor'           => $vendor,
            ':package'          => $package,
            ':description'      => 'This module is awesome!',
            ':author-name'      => $gitUserName ?: 'John Doe',
            ':author-email'     => $gitUserEmail ?: 'john.doe@example.com',
            ':author-github'    => $vendor,
            ':module-namespace' => $moduleNamespace,
            ':module-name'      => $moduleName,
            ':company'          => $company,
            ':year'             => (string)date('Y'),
        ];
    }

    /**
     * Exact PHP requirements are already handled by magento/framework itself, but making it explicit allows the module
     * author to tighten the constraints, e.g. make the module work with Magento 2.3 but still require at least PHP 7.3
     *
     * The following placeholder values will be generated automatically based on choosen compatible versions:
     *
     * @return string[][][] Associative array with Magento versions and dependencies for composer
     * @see Initialize::askMagentoCompatibility()
     *
     *  :php-constraint         Composer version constraint for PHP
     *  :framework-constraint   Composer version constraint for Magento framework
     *  :version-badge          Text for the "Magento" compatibility badge
     *
     */
    public static function getMagentoVersions(): array
    {
        return [
            '2.2' => [
                'php'               => ['~7.0', '~7.1', '~7.2'],
                'magento-framework' => ['^101.0.0'],
            ],
            '2.3' => [
                'php'               => ['~7.1', '~7.2', '~7.3'],
                'magento-framework' => ['^102.0.0'],
            ],
            '2.4' => [
                'php'               => ['~7.3', '~7.4'],
                'magento-framework' => ['^103.0.0'],
            ],
        ];
    }

    /**
     * package prefixes that should not be part of the module name (capitalized because used after ucwords())
     *
     * This covers e.g.:
     *
     *      magento2-
     *      magento2-module
     *
     * @param string $moduleNamespace
     * @return string[]
     */
    private static function getCommonModulePrefixes(string $moduleNamespace): array
    {
        return ['Magento2', 'Module', $moduleNamespace];
    }
}
