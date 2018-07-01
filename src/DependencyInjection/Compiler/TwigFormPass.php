<?php

namespace FM\TinyMCEBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class TwigFormPass.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class TwigFormPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('twig.form.resources')) {
            return;
        }

        $container->setParameter('twig.form.resources', array_merge(
            array('FMTinyMCEBundle:Form:tinymce_widget.html.twig'),
            $container->getParameter('twig.form.resources')
        ));
    }
}
