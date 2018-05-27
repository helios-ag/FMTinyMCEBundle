<?php

namespace FM\TinyMCEBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class FMTinyMCEExtension.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015-2018 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FMTinyMCEExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form.xml');
        $loader->load('templating.xml');
        $loader->load('twig.xml');

        $container->setParameter('fm_tinymce', $config);
        $container->setParameter('fm_tinymce.instances', $config['instances']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'fm_tinymce';
    }
}
