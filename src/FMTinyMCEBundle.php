<?php

namespace FM\TinyMCEBundle;

use FM\TinyMCEBundle\DependencyInjection\FMTinyMCEExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use FM\TinyMCEBundle\DependencyInjection\Compiler\TwigFormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class FMTinyMCEBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigFormPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new FMTinyMCEExtension();
        }

        return $this->extension;
    }
}
