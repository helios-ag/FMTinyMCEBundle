<?php

namespace FM\TinyMCEBundle\Tests;

use FM\TinyMCEBundle\FMTinyMCEBundle;

/**
 * Class FMTinyMCEBundleTest.
 */
class FMTinyMCEBundleTest extends \PHPUnit\Framework\TestCase
{
    public function testIsBundle()
    {
        $bundle = new FMTinyMCEBundle();

        $this->assertInstanceOf('Symfony\Component\HttpKernel\Bundle\Bundle', $bundle);
    }

    public function testCompilerPasses()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['addCompilerPass'])
            ->getMock();
        $containerBuilder
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('FM\TinyMCEBundle\DependencyInjection\Compiler\TwigFormPass'))
            ->willReturnSelf();
        $bundle = new FMTinyMCEBundle();
        $bundle->build($containerBuilder);
    }
}
