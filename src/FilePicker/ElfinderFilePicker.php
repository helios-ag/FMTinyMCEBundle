<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\FilePicker;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ElfinderFilePicker
{
    public function __construct(private readonly UrlGeneratorInterface $urls)
    {
    }

    /** @param array<string, mixed> $configuration
     *  @return array<string, string>
     */
    public function build(array $configuration, string $instanceName): array
    {
        if (null === ($configuration['type'] ?? null)) {
            return [];
        }

        if ('fm_elfinder' !== $configuration['type']) {
            throw new \InvalidArgumentException(sprintf('Unsupported file picker type for fm_tinymce.instances.%s.file_picker.', $instanceName));
        }

        $route = $configuration['route'] ?? null;
        if (!is_string($route) || '' === $route) {
            throw new \InvalidArgumentException(sprintf('The fm_tinymce.instances.%s.file_picker.route value is required for fm_elfinder.', $instanceName));
        }

        $parameters = $configuration['route_parameters'] ?? [];
        if (!is_array($parameters)) {
            throw new \InvalidArgumentException(sprintf('The fm_tinymce.instances.%s.file_picker.route_parameters value must be an array.', $instanceName));
        }

        return [
            'fm_elfinder_url' => $this->urls->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH),
        ];
    }
}
