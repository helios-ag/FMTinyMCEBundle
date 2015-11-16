<?php

namespace FM\TinyMCEBundle;

use FM\TinyMCEBundle\DependencyInjection\FMTinyMCEExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use FM\TinyMCEBundle\DependencyInjection\Compiler\TwigFormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TinyMCEBundle.
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FMTinyMCEBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigFormPass());
    }

    /**
     * @return FMTinyMCEExtension
     *                            Redefining root node
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new FMTinyMCEExtension();
        }

        return $this->extension;
    }
}
