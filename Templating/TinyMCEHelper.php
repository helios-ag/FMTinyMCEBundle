<?php

namespace FM\TinyMCEBundle\Templating;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\Helper\Helper;
use Ivory\JsonBuilder\JsonBuilder;
use Symfony\Component\Asset\PackageInterface;
/**
 * Class TinyMCEHelper
 * @package FM\TinyMCEBundle\Templating
 */
class TinyMCEHelper extends Helper
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Packages
     */
    protected $assetsHelper;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * TinyMCEHelper constructor.
     * @param array $parameters
     * @param Router $router
     * @param Packages $assetsHelper
     */
    public function __construct($parameters, Router $router, Packages $assetsHelper)
    {
        $this->router       = $router;
        $this->assetsHelper = $assetsHelper;
        $this->parameters   = $parameters;
    }


    /**
     * @param $instance
     * @return string
     */
    public function renderFileBrowser($instance)
    {
        if($fileBrowser = $this->parameters[$instance]['file_picker_callback']) {
            return 'file_picker_callback: ' . $fileBrowser;
        }
        return '';
    }

    /**
     * @param $instance
     * @return string
     */
    public function fileBrowserPathHelper($instance)
    {
        $path = '';
        if($fileBrowser = $this->parameters[$instance]['filebrowser']) {
            $path = $this->router->generate($fileBrowser['route'], $fileBrowser['route_parameters']);
        } elseif ($fileBrowser['url'])
            $path = $fileBrowser['url'];
        return $path;
    }

    /**
     * @param $path
     * @return string
     */
    public function renderBasePath($path)
    {
        return $this->assetsHelper->getUrl($path);
    }

    /**
     * @param array $config
     * @return array
     */
    public function fixConfigLanguage(array $config)
    {
        if (isset($config['language'])) {
            $config['language'] = strtolower(str_replace('_', '-', $config['locale']));
        }
        return $config;
    }

    /**
     * @param $instance
     * @return array
     */
    public function getToolbars($instance)
    {
        $toolbarsString = '';
        $toolbars = $this->parameters[$instance]['toolbars'];
        foreach ($toolbars as $toolbarName => $toolbar) {
            $toolbarsString .= sprintf(
                '%s: "%s",',
                $toolbarName, $toolbar);
        }
        return $toolbarsString;
    }

    /**
     * @param $instance
     * @return string
     */
    public function getTheme($instance)
    {
        return sprintf('theme: "%s",',$this->parameters[$instance]['theme']);
    }

    /**
     * @param $instance
     * @return string
     */
    public function getPlugins($instance)
    {
        $plugins = $this->parameters[$instance]['plugins'];

        return sprintf('plugins: [%s],', $plugins);
    }

    /**
     * @param $instance
     * @return string
     */
    public function getTemplates($instance)
    {
        $templateString = '';
        $templates = array();
        if(array_key_exists('templates', $this->parameters[$instance])) {
            $templates = $this->parameters[$instance]['templates'];
        }

        foreach ($templates as $template) {
            $templateString .= sprintf('{ title: %s, content: %s },', $template['title'], $template['content']);
        }

        return sprintf('templates: [%s],', $templateString);
    }

    public function getWidth($instance)
    {
        return sprintf("width: %s,", $this->parameters[$instance]['width']);
    }

    public function getHeight($instance)
    {
        return sprintf("height: %s,",$this->parameters[$instance]['height']);
    }

    /**
     * @param $instance
     * @return string
     */
    public function getImgAdvTab($instance)
    {
        $imgAdvTab = $this->parameters[$instance]['image_advtab'] ? 'true' : 'false';

        return sprintf('image_advtab: %s,', $imgAdvTab);
    }

    /**
     * @param $instance
     * @return string
     */
    public function getMenubar($instance)
    {
        $menubar = $this->parameters[$instance]['menubar'];

        return sprintf('menubar: %s,', $menubar);
    }

    /**
     * @param $instance
     * @return string
     */
    public function getToolbarItemSize($instance)
    {
        return sprintf('toolbar_items_size: "%s",', $this->parameters[$instance]['toolbar_item_size']);
    }

    /**
     * @param $instance
     * @return mixed
     */
    public function renderFileBrowserType($instance)
    {
        return $this->parameters[$instance]['filebrowser_type'];
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'fm_tinymce';
    }

}