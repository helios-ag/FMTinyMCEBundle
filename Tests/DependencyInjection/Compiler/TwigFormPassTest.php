<?php

namespace FM\TinyMCEBundle\Tests\Compiler;

use FM\TinyMCEBundle\DependencyInjection\Compiler\TwigFormPass;

/**
 * Class TwigFormPassTest
 * @package FM\TinyMCEBundle\Tests\Compiler
 */
class TwigFormPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigFormPass
     */
    private $compilerPass;

    protected function setUp()
    {
        $this->compilerPass = new TwigFormPass();
    }

    protected function tearDown()
    {
        unset($this->compilerPass);
    }

    /**
     * Creates a container builder mock.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'setParameter'))
            ->getMock();
    }

    public function testTwigFormPass()
    {
        $this->markTestIncomplete('need to work more');
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array('templating.engines', array('twig')),
                array($parameter = 'twig.form.resources', array($template = 'foo')),
            )));
        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo(array('FMTinyMCEBundle:Form:tinymce_widget.html.twig', $template))
            );
        $this->compilerPass->process($containerBuilder);
    }
}
