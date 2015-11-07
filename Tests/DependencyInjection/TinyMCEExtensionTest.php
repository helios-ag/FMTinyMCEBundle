<?php

namespace FM\TinyMCEBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use FM\TinyMCEBundle\DependencyInjection\FMTinyMCEExtension;
use FM\TinyMCEBundle\Tests\Fixtures\Extension\FrameworkExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 * @author Al Ganiev <helios.ag@gmail.com>
 * Class TinyMCEExtensionTest
 */
class TinyMCEExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /** @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $routerMock;

    /** @var \Symfony\Component\Asset\Packages|\Symfony\Component\Templating\Helper\CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $assetsHelperMock;

    protected function setUp()
    {
        if (class_exists('Symfony\Component\Asset\Packages')) {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Asset\Packages')
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
                ->disableOriginalConstructor()
                ->getMock();
        }

        $this->routerMock = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->container  = new ContainerBuilder();

        $this->container->set('assets.packages', $this->assetsHelperMock);
        $this->container->set('router', $this->routerMock);
        $this->container->registerExtension($framework = new FrameworkExtension());
        $this->container->loadFromExtension($framework->getAlias());
        $this->container->registerExtension($tinymce = new FMTinyMCEExtension());
        $this->container->loadFromExtension($tinymce->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->container);
    }

    public function testDefaultFormType()
    {
        $this->loadConfiguration($this->container, 'simple');
        $this->container->compile();
        /** @var \FM\TinyMCEBundle\Form\Type\TinyMCEType $type */
        $type = $this->container->get('fm_tinymce.form.type');
        $this->assertInstanceOf('FM\TinyMCEBundle\Form\Type\TinyMCEType', $type);
    }

    public function testTemplatingConfiguration()
    {
        $this->loadConfiguration($this->container, 'simple');
        $this->container->compile();
        $helper = $this->container->get('fm_tinymce.templating.helper');
        $this->assertInstanceOf('FM\TinyMCEBundle\Templating\TinyMCEHelper', $helper);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadConfiguration(ContainerBuilder $container, $configuration)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Fixtures/config/DI/'));
        $loader->load($configuration.'.yml');
    }
}
