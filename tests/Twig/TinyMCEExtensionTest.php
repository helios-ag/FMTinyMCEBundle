<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\Twig;

use FM\TinyMCEBundle\Configuration\InstanceConfigurationResolver;
use FM\TinyMCEBundle\Configuration\TinyMCEConfigurationBuilder;
use FM\TinyMCEBundle\Twig\TinyMCEExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

final class TinyMCEExtensionTest extends TestCase
{
    public function testItReturnsHtmlSafeTinyMceConfiguration(): void
    {
        $packages = new Packages(new Package(new EmptyVersionStrategy()));
        $extension = new TinyMCEExtension(new TinyMCEConfigurationBuilder(
            new InstanceConfigurationResolver([
                'default' => [
                    'enabled' => true,
                    'inline' => false,
                    'options' => ['toolbar' => 'undo redo'],
                ],
            ]),
            $packages,
            'assets/tinymce',
        ), $packages);

        $configuration = $extension->configuration('article_body', 'default', true);

        self::assertSame('gpl', json_decode($configuration, true, flags: JSON_THROW_ON_ERROR)['license_key']);
        self::assertSame('#article_body', json_decode($configuration, true, flags: JSON_THROW_ON_ERROR)['selector']);
        self::assertSame('tinymce_configuration', $extension->getFunctions()[0]->getName());
    }

    public function testItDelegatesAssetUrlsToSymfonyAssetPackages(): void
    {
        $packages = new Packages(new Package(new EmptyVersionStrategy()));
        $extension = new TinyMCEExtension(new TinyMCEConfigurationBuilder(
            new InstanceConfigurationResolver(['default' => ['enabled' => true, 'inline' => false, 'options' => []]]),
            $packages,
            'assets/tinymce',
        ), $packages);

        self::assertSame('assets/tinymce/tinymce.min.js', $extension->assetUrl('assets/tinymce/tinymce.min.js'));
    }
}
