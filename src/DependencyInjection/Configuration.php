<?php

namespace FM\TinyMCEBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('fm_tinymce');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('instance')
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('inline')->defaultFalse()->end()
                ->scalarNode('base_path')->defaultValue('assets/tinymce/')->end()
                ->scalarNode('js_path')->defaultValue('assets/tinymce/tinymce.min.js')->end()
                ->arrayNode('instances')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('language')->defaultValue('en_GB')->end()
                            ->scalarNode('width')->defaultValue(600)->end()
                            ->scalarNode('height')->defaultValue(300)->end()
                            ->scalarNode('theme')->defaultValue('modern')->end()
                            ->scalarNode('toolbar_item_size')->defaultValue('small')->end()
                            ->scalarNode('menubar')->defaultValue($this->getDefaultMenubar())->end()
                            ->booleanNode('image_advtab')->defaultFalse()->end()
                            ->append($this->createTemplateNode())
                            ->scalarNode('plugins')->defaultValue($this->getDefaultPlugins())->end()
                            ->booleanNode('relative_urls')->defaultFalse()->end()
                            ->booleanNode('convert_urls')->defaultFalse()->end()
                            ->append($this->createToolbarsNode()) //toolbars node
                            ->scalarNode('filebrowser_type')->defaultValue('fm_elfinder')->end()
                            ->scalarNode('file_picker_callback')->defaultNull()->end()
                            ->arrayNode('filebrowser')
                                ->addDefaultsIfNotSet()
                                ->beforeNormalization()
                                ->ifString()
                                    ->then(function ($v) {
                                        return ['url' => $v];
                                    })
                                ->end()
                                ->children()
                                    ->scalarNode('url')->defaultNull()->end() //can be set via straight url
                                    ->scalarNode('route')->defaultNull()->end() // or via route
                                    ->variableNode('route_parameters')->defaultValue([])->end()
                                ->end()
                            ->end()
                ->end();

        return $treeBuilder;
    }

    /**
     * @return mixed
     */
    private function createTemplateNode()
    {
        return $this->createNode('templates')
                    ->fixXmlConfig('template')
                        ->children()
                            ->arrayNode('templates')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('title')->end()
                                        ->scalarNode('content')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end();
    }

    private function createToolbarsNode(): NodeDefinition
    {
        return $this->createNode('toolbars')
            ->useAttributeAsKey('name')
                ->prototype('scalar')
            ->end();
    }

    private function createNode(string $name): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($name);

        return $treeBuilder->getRootNode();
    }

    private function getDefaultPlugins(): string
    {
        return 'advlist autolink lists link image charmap print preview anchor,
        searchreplace visualblocks code fullscreen,
        insertdatetime media table contextmenu paste';
    }

    private function getDefaultMenubar(): string
    {
        return 'file edit insert view format table tools';
    }
}
