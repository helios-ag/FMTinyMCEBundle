<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class FMTinyMCEExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $container->setParameter('fm_tinymce', $config);
        $container->setParameter('fm_tinymce.instances', $config['instances']);
        $container->setParameter('fm_tinymce.assets.base_path', $config['assets']['base_path']);
        $container->setParameter('fm_tinymce.assets.script_path', $config['assets']['script_path']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'form_themes' => ['@FMTinyMCE/Form/tinymce_widget.html.twig'],
        ]);
    }

    public function getAlias(): string
    {
        return 'fm_tinymce';
    }
}
