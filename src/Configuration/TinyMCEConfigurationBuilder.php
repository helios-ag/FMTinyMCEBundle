<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Configuration;

use FM\TinyMCEBundle\FilePicker\ElfinderFilePicker;
use Symfony\Component\Asset\Packages;

final class TinyMCEConfigurationBuilder
{
    public function __construct(
        private readonly InstanceConfigurationResolver $instances,
        private readonly Packages $packages,
        private readonly string $basePath,
        private readonly ?ElfinderFilePicker $filePicker = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function build(string $fieldId, string $instanceName, ?bool $enabledOverride = null): array
    {
        $instance = $this->instances->resolve($instanceName, $enabledOverride);
        $options = $instance['options'] ?? [];

        if (!is_array($options)) {
            throw new \LogicException(sprintf('The "%s" TinyMCE instance has invalid options.', $instanceName));
        }

        unset($options['file_picker_callback'], $options['license_key'], $options['selector']);

        $baseUrl = $this->packages->getUrl($this->basePath);
        $options['base_url'] = substr($baseUrl, 0, strcspn($baseUrl, '?#'));
        $options['inline'] = (bool) ($instance['inline'] ?? false);
        $options['license_key'] = 'gpl';
        $options['selector'] = '#'.$fieldId;

        if (null !== $this->filePicker) {
            $filePickerConfiguration = $instance['file_picker'] ?? [];
            if (!is_array($filePickerConfiguration)) {
                throw new \LogicException(sprintf('The "%s" TinyMCE instance has invalid file picker configuration.', $instanceName));
            }

            /** @var array<string, mixed> $filePickerConfiguration */
            $options += $this->filePicker->build($filePickerConfiguration, $instanceName);
        }

        $configuration = [];
        foreach ($options as $key => $value) {
            if (!is_string($key)) {
                throw new \LogicException(sprintf('The "%s" TinyMCE instance has a non-string option key.', $instanceName));
            }

            $configuration[$key] = $value;
        }

        return $configuration;
    }

    /** @param array<string, mixed> $configuration */
    public function encodeForScript(array $configuration): string
    {
        try {
            return json_encode(
                $configuration,
                JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
            );
        } catch (\JsonException $exception) {
            throw new \LogicException('Unable to encode TinyMCE configuration.', previous: $exception);
        }
    }
}
