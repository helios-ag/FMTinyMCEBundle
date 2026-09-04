<?php

namespace FM\TinyMCEBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function getRootDir()
    {
        return __DIR__.'/Fixtures';
    }

    public function registerBundles(): iterable
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \FM\TinyMCEBundle\FMTinyMCEBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/Fixtures/config/config.yml');
    }
}
