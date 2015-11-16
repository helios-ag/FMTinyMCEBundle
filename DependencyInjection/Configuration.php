<?php

namespace FM\TinyMCEBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('fm_tinymce');

        $rootNode
            ->fixXmlConfig('instance')
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('inline')->defaultFalse()->end()
                ->scalarNode('base_path')->defaultValue('bundles/fmtinymce/')->end()
                ->scalarNode('js_path')->defaultValue('bundles/fmtinymce/tinymce.min.js')->end()
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
                                    ->then(function ($v) { return array('url' => $v); })
                                ->end()
                                ->children()
                                    ->scalarNode('url')->defaultNull()->end() //can be set via straight url
                                    ->scalarNode('route')->defaultNull()->end() // or via route
                                    ->variableNode('route_parameters')->defaultValue(array())->end()
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

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition The toolbars node.
     */
    private function createToolbarsNode()
    {
        return $this->createNode('toolbars')
            ->useAttributeAsKey('name')
                ->prototype('array')
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();
    }

    /**
     * Creates a node.
     *
     * @param string $name The node name.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition The node.
     */
    private function createNode($name)
    {
        return $this->createTreeBuilder()->root($name);
    }

    /**
     * Creates a tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder.
     */
    private function createTreeBuilder()
    {
        return new TreeBuilder();
    }

    /**
     * @return string
     */
    private function getDefaultPlugins()
    {
        return '"advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"';
    }

    /**
     * @return string
     */
    private function getDefaultMenubar()
    {
        return 'file edit insert view format table tools';
    }
}
