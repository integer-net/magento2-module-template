<?php

namespace :module-namespace\:module-name;

use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppIsolation enabled
 */
class ExampleTest extends TestCase
{
    /** @test */
    public function we_can_use_the_object_manager()
    {
        $this->assertInstanceOf(Magento\TestFramework\ObjectManager::class, Bootstrap::getObjectManager());
    }
}
