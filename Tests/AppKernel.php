<?php

namespace FM\TinyMCEBundle\Tests;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 * @package FM\TinyMCEBundle\Tests
 */
class AppKernel extends Kernel
{

    public function getRootDir()
    {
        return __DIR__.'/Fixtures';
    }

    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \FM\TinyMCEBundle\FMTinyMCEBundle(),
        );
        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/Fixtures/config/config.yml');
    }
}