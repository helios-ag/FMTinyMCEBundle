<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\DependencyInjection;

use FM\TinyMCEBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testItNormalizesTheDefaultTinyMceEightProfile(): void
    {
        $configuration = $this->process([
            'instances' => [
                'default' => [
                    'options' => [
                        'language' => 'en',
                        'plugins' => ['link', 'image'],
                        'toolbar' => 'undo redo | link image',
                    ],
                ],
            ],
        ]);

        self::assertSame('assets/tinymce', $configuration['assets']['base_path']);
        self::assertSame('assets/tinymce/tinymce.min.js', $configuration['assets']['script_path']);
        self::assertTrue($configuration['instances']['default']['enabled']);
        self::assertFalse($configuration['instances']['default']['inline']);
        self::assertSame(['link', 'image'], $configuration['instances']['default']['options']['plugins']);
    }

    public function testItRejectsPluginsRemovedBeforeTinyMceEight(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('print');
        $this->expectExceptionMessage('TinyMCE 8');

        $this->process([
            'instances' => [
                'default' => [
                    'options' => [
                        'plugins' => ['link', 'print'],
                    ],
                ],
            ],
        ]);
    }

    public function testItRejectsTheModernThemeRemovedBeforeTinyMceEight(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('modern');
        $this->expectExceptionMessage('TinyMCE 8');

        $this->process([
            'instances' => [
                'default' => [
                    'options' => ['theme' => 'modern'],
                ],
            ],
        ]);
    }

    /** @param array<string, mixed> $configuration */
    private function process(array $configuration): array
    {
        return (new Processor())->processConfiguration(new Configuration(), [$configuration]);
    }
}
