<?php

namespace FM\TinyMCEBundle\Twig;

use FM\TinyMCEBundle\Templating\TinyMCEHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TinyMCEExtenion.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class TinyMCEExtension extends AbstractExtension
{
    /**
     * @var TinyMCEHelper
     */
    private $helper;

    /**
     * TinyMCEExtension constructor.
     */
    public function __construct(TinyMCEHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = ['is_safe' => ['html']];

        return [
            new TwigFunction('tinymce_path', [$this, 'renderJsPath'], $options),
            new TwigFunction('tinymce_file_picker_callback', [$this, 'renderFilePicker'], $options),
            new TwigFunction('tinymce_filebrowser_path', [$this, 'renderFilebrowserPath'], $options),
            new TwigFunction('tinymce_filebrowser_type', [$this, 'renderFileBrowserType'], $options),
            new TwigFunction('tinymce_language', [$this, 'renderLanguage'], $options),
            new TwigFunction('tinymce_relative_urls', [$this, 'renderRelativeUrls'], $options),
            new TwigFunction('tinymce_convert_urls', [$this, 'renderConvertUrls'], $options),
            new TwigFunction('tinymce_toolbars', [$this, 'renderToolbars'], $options),
            new TwigFunction('tinymce_plugins', [$this, 'renderPlugins'], $options),
            new TwigFunction('tinymce_theme', [$this, 'renderTheme'], $options),
            new TwigFunction('tinymce_width', [$this, 'renderWidth'], $options),
            new TwigFunction('tinymce_height', [$this, 'renderHeight'], $options),
            new TwigFunction('tinymce_image_advtab', [$this, 'renderImgAdvTab'], $options),
            new TwigFunction('tinymce_menubar', [$this, 'renderMenubar'], $options),
            new TwigFunction('tinymce_templates', [$this, 'renderTemplates'], $options),
            new TwigFunction('tinymce_toolbar_items_size', [$this, 'renderToolbarItemSize'], $options),
        ];
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function renderJsPath($path)
    {
        return $this->helper->getJsPath($path);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderFilePicker($instance)
    {
        return $this->helper->getFilePickerCallback($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderFileBrowserType($instance)
    {
        return $this->helper->getFileBrowserType($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderFileBrowserPath($instance)
    {
        return $this->helper->getFileBrowserPathHelper($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderLanguage($instance)
    {
        return $this->helper->getLanguage($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderRelativeUrls($instance)
    {
        return $this->helper->getRelativeUrls($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderConvertUrls($instance)
    {
        return $this->helper->getConvertUrls($instance);
    }

    /**
     * @param $instance
     *
     * @return mixed
     */
    public function renderToolbarItemSize($instance)
    {
        return $this->helper->getToolbarItemSize($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderTemplates($instance)
    {
        return $this->helper->getTemplates($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderTheme($instance)
    {
        return $this->helper->getTheme($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderImgAdvTab($instance)
    {
        return $this->helper->getImgAdvTab($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderMenubar($instance)
    {
        return $this->helper->getMenubar($instance);
    }

    /**
     * @param $instance
     *
     * @return mixed
     */
    public function renderWidth($instance)
    {
        return $this->helper->getWidth($instance);
    }

    /**
     * @param $instance
     *
     * @return mixed
     */
    public function renderHeight($instance)
    {
        return $this->helper->getHeight($instance);
    }

    /**
     * @param $instance
     *
     * @return array
     */
    public function renderToolbars($instance)
    {
        return $this->helper->getToolbars($instance);
    }

    /**
     * @param $instance
     *
     * @return string
     */
    public function renderPlugins($instance)
    {
        return $this->helper->getPlugins($instance);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function renderBasePath($path)
    {
        return $this->helper->getBasePath($path);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return $this->helper->getName();
    }
}
