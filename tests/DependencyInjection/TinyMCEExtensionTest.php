<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\DependencyInjection;

use FM\TinyMCEBundle\Configuration\TinyMCEConfigurationBuilder;
use FM\TinyMCEBundle\DependencyInjection\FMTinyMCEExtension;
use FM\TinyMCEBundle\Form\Type\TinyMCEType;
use FM\TinyMCEBundle\Twig\TinyMCEExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TinyMCEExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new FMTinyMCEExtension(),
        ];
    }

    public function testItLoadsModernServiceDefinitions(): void
    {
        $this->load([
            'instances' => [
                'default' => ['options' => []],
            ],
        ]);

        $this->assertContainerBuilderHasService(TinyMCEType::class);
        $this->assertContainerBuilderHasService(TinyMCEExtension::class);
        $this->assertContainerBuilderHasService(TinyMCEConfigurationBuilder::class);
        self::assertSame('assets/tinymce', $this->container->getParameter('fm_tinymce.assets.base_path'));
    }

    public function testItPrependsItsTwigFormTheme(): void
    {
        $container = new ContainerBuilder();

        (new FMTinyMCEExtension())->prepend($container);

        self::assertSame([
            ['form_themes' => ['@FMTinyMCE/Form/tinymce_widget.html.twig']],
        ], $container->getExtensionConfig('twig'));
    }
}
