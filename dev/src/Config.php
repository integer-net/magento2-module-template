<?php
namespace IntegerNet\ModuleTemplate;

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
        /**
         * @todo Allow storing default variables in environment or git config
         */
        return [
            ':vendor'             => 'acme-inc',
            ':package'            => 'magento2-awesome-module',
            ':description'        => 'This module is awesome!',
            ':author-name'        => trim(`git config --get user.name`) ?: 'John Doe',
            ':author-email'       => trim(`git config --get user.email`) ?: 'john.doe@example.com',
            ':author-github'      => 'acme-developer',
            ':module-namespace'   => 'Acme',
            ':module-name'        => 'AwesomeModule',
            ':company'            => 'ACME Inc.',
            ':year'               => (string)date('Y'),
        ];
    }

    /**
     * Exact PHP requirements are already handled by magento/framework itself, but making it explicit allows the module
     * author to tighten the constraints, e.g. make the module work with Magento 2.3 but still require at least PHP 7.3
     *
     * The following placeholder values will be generated automatically based on choosen compatible versions:
     *
     * @see Initialize::askMagentoCompatibility()
     *
     *  :php-constraint         Composer version constraint for PHP
     *  :framework-constraint   Composer version constraint for Magento framework
     *  :version-badge          Text for the "Magento" compatibility badge
     *
     * @return string[][][] Associative array with Magento versions and dependencies for composer
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
}
