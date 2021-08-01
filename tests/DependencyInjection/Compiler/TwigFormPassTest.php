<?php

namespace FM\TinyMCEBundle\Tests\Compiler;

use FM\TinyMCEBundle\DependencyInjection\Compiler\TwigFormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigFormPassTest.
 */
class TwigFormPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $pass      = new TwigFormPass();
        $pass->process($container);
        $this->assertFalse($container->hasParameter('twig.form.resources'));
        $container = new ContainerBuilder();
        $container->setParameter('twig.form.resources', []);
        $pass->process($container);
        $this->assertEquals([
            'FMTinyMCEBundle:Form:tinymce_widget.html.twig',
        ], $container->getParameter('twig.form.resources'));
    }
}
