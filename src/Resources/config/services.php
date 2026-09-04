<?php

declare(strict_types=1);

use FM\TinyMCEBundle\Configuration\InstanceConfigurationResolver;
use FM\TinyMCEBundle\Configuration\TinyMCEConfigurationBuilder;
use FM\TinyMCEBundle\FilePicker\ElfinderFilePicker;
use FM\TinyMCEBundle\Form\Type\TinyMCEType;
use FM\TinyMCEBundle\Twig\TinyMCEExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()->defaults()->autowire()->autoconfigure();

    $services->set(InstanceConfigurationResolver::class)
        ->arg('$instances', '%fm_tinymce.instances%');
    $services->set(ElfinderFilePicker::class);
    $services->set(TinyMCEConfigurationBuilder::class)
        ->arg('$basePath', '%fm_tinymce.assets.base_path%');
    $services->set(TinyMCEType::class)
        ->arg('$scriptPath', '%fm_tinymce.assets.script_path%')
        ->tag('form.type');
    $services->set(TinyMCEExtension::class)->tag('twig.extension');
};
