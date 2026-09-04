<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Twig;

use FM\TinyMCEBundle\Configuration\TinyMCEConfigurationBuilder;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TinyMCEExtension extends AbstractExtension
{
    public function __construct(
        private readonly TinyMCEConfigurationBuilder $configurationBuilder,
        private readonly Packages $packages,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tinymce_configuration', $this->configuration(...), ['is_safe' => ['html']]),
            new TwigFunction('tinymce_asset_url', $this->assetUrl(...)),
        ];
    }

    public function configuration(string $fieldId, string $instance, ?bool $enabled = null): string
    {
        return $this->configurationBuilder->encodeForScript(
            $this->configurationBuilder->build($fieldId, $instance, $enabled),
        );
    }

    public function assetUrl(string $path): string
    {
        return $this->packages->getUrl($path);
    }
}
