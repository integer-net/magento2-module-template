<?php
declare(strict_types=1);

namespace IntegerNet\ModuleTemplate;

use PHPUnit\Framework\TestCase;

class TravisJobTest extends TestCase
{
    /**
     * @dataProvider yamlExpectation
     */
    public function testYamlOutput(array $magentoVersions, string $expectedYaml)
    {
        $this->assertEquals($expectedYaml, TravisJob::getConfiguration($magentoVersions));
    }

    public function yamlExpectation()
    {
        yield 'Magento 2.3 | 2.4' => [
            ['2.3', '2.4'],
            <<<YAML
jobs:
  include:
    -
      php: "7.1"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=integration"

    -
      php: "7.2"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=integration"

    -
      php: "7.3"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=integration"

    -
      php: "7.3"
      env:
        - "MAGENTO_VERSION=2.4"
        - "TEST_SUITE=integration"

    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4"
        - "TEST_SUITE=integration"
        - "COVERAGE=true"

    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4"
        - "TEST_SUITE=unit"
        - "COVERAGE=true"

    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4-develop"
        - "TEST_SUITE=integration"

  allow_failures:
    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4-develop"
        - "TEST_SUITE=integration"


YAML
    ,
        ];
        yield 'Magento 2.4' => [
            ['2.4'],
            <<<YAML
jobs:
  include:
    -
      php: "7.3"
      env:
        - "MAGENTO_VERSION=2.4"
        - "TEST_SUITE=integration"

    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4"
        - "TEST_SUITE=integration"
        - "COVERAGE=true"

    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4"
        - "TEST_SUITE=unit"
        - "COVERAGE=true"

    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4-develop"
        - "TEST_SUITE=integration"

  allow_failures:
    -
      php: "7.4"
      env:
        - "MAGENTO_VERSION=2.4-develop"
        - "TEST_SUITE=integration"


YAML,
        ];        yield 'Magento 2.3' => [
            ['2.3'],
            <<<YAML
jobs:
  include:
    -
      php: "7.1"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=integration"

    -
      php: "7.2"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=integration"

    -
      php: "7.3"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=integration"
        - "COVERAGE=true"

    -
      php: "7.3"
      env:
        - "MAGENTO_VERSION=2.3"
        - "TEST_SUITE=unit"
        - "COVERAGE=true"


YAML,
        ];
    }
}