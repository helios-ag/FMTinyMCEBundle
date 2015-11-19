<?php

namespace FM\TinyMCEBundle\Templating;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Class TinyMCEHelper.
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
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
     *
     * @param array           $parameters
     * @param RouterInterface $router
     * @param Packages        $assetsHelper
     */
    public function __construct($parameters, RouterInterface $router, Packages $assetsHelper)
    {
        $this->router       = $router;
        $this->assetsHelper = $assetsHelper;
        $this->parameters   = $parameters;
    }

    /**
     * gets the js path.
     *
     * @param string $jsPath The js path.
     *
     * @return string The rendered js path.
     */
    public function getJsPath($jsPath)
    {
        return $this->assetsHelper->getUrl($jsPath);
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function getBasePath($path)
    {
        return $this->fixPath($this->assetsHelper->getUrl($path));
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getFilePickerCallback($instance = 'default')
    {
        if ($fileBrowser = $this->parameters[$instance]['file_picker_callback']) {
            return sprintf('file_picker_callback: %s,', $fileBrowser);
        }

        return '';
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getFileBrowserPathHelper($instance = 'default')
    {
        $path = '';
        if ($fileBrowser = $this->parameters[$instance]['filebrowser']) {
            $path = $this->router->generate($fileBrowser['route'], $fileBrowser['route_parameters']);
        } elseif ($fileBrowser['url']) {
            $path = $fileBrowser['url'];
        }

        return $path;
    }

    /**
     * @param string $instance
     *
     * @return array
     */
    protected function fixConfigLanguage($instance = 'default')
    {
        $locale = 'en_US';
        if (isset($this->parameters[$instance]['language'])) {
            $locale = strtolower(str_replace('-', '_', $this->parameters[$instance]['language']));
        }

        return $locale;
    }

    private function fixPath($path)
    {
        if (($position = strpos($path, '?')) !== false) {
            return substr($path, 0, $position);
        }

        return $path;
    }

    /**
     * @param string $instance
     *
     * @return string
     */
    public function getLanguage($instance = 'default')
    {
        return sprintf('language: "%s",', $this->fixConfigLanguage($instance));
    }

    /**
     * @param $instance
     *
     * @return array
     */
    public function getToolbars($instance = 'default')
    {
        $toolbarsString = '';
        $toolbars       = $this->parameters[$instance]['toolbars'];
        foreach ($toolbars as $toolbarName => $toolbar) {
            $toolbarsString .= sprintf(
                '%s: "%s",',
                $toolbarName, $toolbar);
        }

        return $toolbarsString;
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getToolbarItemSize($instance = 'default')
    {
        return sprintf('toolbar_items_size: "%s",', $this->parameters[$instance]['toolbar_item_size']);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getTheme($instance = 'default')
    {
        return sprintf('theme: "%s",', $this->parameters[$instance]['theme']);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getPlugins($instance = 'default')
    {
        $plugins = $this->parameters[$instance]['plugins'];

        return sprintf('plugins: [%s],', $plugins);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getTemplates($instance = 'default')
    {
        $templateString = '';
        $templates      = array();
        if (array_key_exists('templates', $this->parameters[$instance])) {
            $templates = $this->parameters[$instance]['templates'];
        }

        foreach ($templates as $template) {
            $templateString .= sprintf('{ title: %s, content: %s },', $template['title'], $template['content']);
        }

        return sprintf('templates: [%s],', $templateString);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getWidth($instance = 'default')
    {
        $width = $this->parameters[$instance]['width'];

        if($width == 'auto' || $width == 'false') return '';

        return sprintf('width: %s,', $width);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getHeight($instance = 'default')
    {
        $height = $this->parameters[$instance]['height'];

        if($height == 'auto' || $height == 'false') return '';

        return sprintf('height: %s,', $height);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getImgAdvTab($instance = 'default')
    {
        $imgAdvTab = $this->parameters[$instance]['image_advtab'] ? 'true' : 'false';

        return sprintf('image_advtab: %s,', $imgAdvTab);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function getMenubar($instance = 'default')
    {
        $menubar = $this->parameters[$instance]['menubar'];

        return sprintf('menubar: "%s",', $menubar);
    }

    /**
     * @param $instance
     *
     * @return mixed
     */
    public function getFileBrowserType($instance = 'default')
    {
        return $this->parameters[$instance]['filebrowser_type'];
    }

    public function getConvertUrls($instance = 'default')
    {
        $value = $this->parameters[$instance]['convert_urls'] ? 'true' : 'false';

        return sprintf('convert_urls: %s,', $value);
    }

    public function getRelativeUrls($instance = 'default')
    {
        $value = $this->parameters[$instance]['relative_urls'] ? 'true' : 'false';

        return sprintf('relative_urls: %s,', $value);
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
