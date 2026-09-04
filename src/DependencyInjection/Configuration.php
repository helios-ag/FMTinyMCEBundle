<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class Configuration implements ConfigurationInterface
{
    /** @var list<string> */
    private const DEFAULT_PLUGINS = [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table',
    ];

    /** @var array<string, string> */
    private const REMOVED_PLUGINS = [
        'bbcode' => 'removed in TinyMCE 6',
        'contextmenu' => 'removed in TinyMCE 6',
        'fullpage' => 'removed in TinyMCE 6',
        'legacyoutput' => 'removed in TinyMCE 6',
        'paste' => 'built into TinyMCE core since TinyMCE 6',
        'print' => 'built into TinyMCE core since TinyMCE 6',
        'spellchecker' => 'removed in TinyMCE 6',
        'tabfocus' => 'removed in TinyMCE 6',
        'template' => 'removed in TinyMCE 7',
        'textcolor' => 'built into TinyMCE core since TinyMCE 6',
    ];

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('fm_tinymce');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('base_path')->defaultValue('assets/tinymce')->cannotBeEmpty()->end()
                        ->scalarNode('script_path')->defaultValue('assets/tinymce/tinymce.min.js')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('instances')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->booleanNode('inline')->defaultFalse()->end()
                            ->variableNode('options')
                                ->defaultValue([
                                    'language' => 'en',
                                    'plugins' => self::DEFAULT_PLUGINS,
                                    'toolbar' => 'undo redo | blocks | bold italic | link image',
                                ])
                                ->validate()
                                    ->always(fn (mixed $options): array => $this->validateOptions($options))
                                ->end()
                            ->end()
                            ->arrayNode('file_picker')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->enumNode('type')->values([null, 'fm_elfinder'])->defaultNull()->end()
                                    ->scalarNode('route')->defaultNull()->end()
                                    ->arrayNode('route_parameters')->normalizeKeys(false)->variablePrototype()->end()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(static fn (array $instances): bool => !array_key_exists('default', $instances))
                        ->thenInvalid('The "fm_tinymce.instances" section must define a "default" instance.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /** @return array<string, mixed> */
    private function validateOptions(mixed $options): array
    {
        if (!is_array($options)) {
            throw new InvalidConfigurationException('The "options" value must be a JSON-compatible map.');
        }

        if (array_key_exists('file_picker_callback', $options)) {
            throw new InvalidConfigurationException('The "file_picker_callback" option is not supported in TinyMCE 8. Use "file_picker.type: fm_elfinder" or configure custom JavaScript in the application.');
        }

        if (isset($options['plugins'])) {
            if (!is_array($options['plugins'])) {
                throw new InvalidConfigurationException('The "options.plugins" value must be a list of plugin names.');
            }

            foreach ($options['plugins'] as $plugin) {
                if (!is_string($plugin)) {
                    throw new InvalidConfigurationException('The "options.plugins" list must contain only strings.');
                }

                if (isset(self::REMOVED_PLUGINS[$plugin])) {
                    throw new InvalidConfigurationException(sprintf('The "%s" plugin is not available in TinyMCE 8: %s.', $plugin, self::REMOVED_PLUGINS[$plugin]));
                }
            }
        }

        $this->assertJsonCompatible($options, 'options');

        return $options;
    }

    private function assertJsonCompatible(mixed $value, string $path): void
    {
        if (null === $value || is_bool($value) || is_int($value) || is_string($value)) {
            return;
        }

        if (is_float($value)) {
            if (is_finite($value)) {
                return;
            }

            throw new InvalidConfigurationException(sprintf('The "%s" option must not contain an infinite or NaN number.', $path));
        }

        if (!is_array($value)) {
            throw new InvalidConfigurationException(sprintf('The "%s" option must be JSON-compatible.', $path));
        }

        foreach ($value as $key => $nestedValue) {
            if (!is_int($key) && !is_string($key)) {
                throw new InvalidConfigurationException(sprintf('The "%s" option contains an invalid key.', $path));
            }

            $this->assertJsonCompatible($nestedValue, sprintf('%s.%s', $path, (string) $key));
        }
    }
}
