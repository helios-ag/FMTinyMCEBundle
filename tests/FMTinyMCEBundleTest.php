<?php

namespace FM\TinyMCEBundle\Tests;

use FM\TinyMCEBundle\FMTinyMCEBundle;

/**
 * Class FMTinyMCEBundleTest.
 */
class FMTinyMCEBundleTest extends \PHPUnit_Framework_TestCase
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
            ->setMethods(array('addCompilerPass'))
            ->getMock();
        $containerBuilder
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('FM\TinyMCEBundle\DependencyInjection\Compiler\TwigFormPass'))
            ->will($this->returnSelf());
        $bundle = new FMTinyMCEBundle();
        $bundle->build($containerBuilder);
    }
}
