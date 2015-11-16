<?php

namespace FM\TinyMCEBundle\Twig;

use FM\TinyMCEBundle\Templating\TinyMCEHelper;

/**
 * Class TinyMCEExtenion.
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class TinyMCEExtension extends \Twig_Extension
{
    /**
     * @var TinyMCEHelper
     */
    private $helper;

    /**
     * TinyMCEExtension constructor.
     *
     * @param TinyMCEHelper $helper
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
        $options = array('is_safe' => array('html'));

        return array(
            new \Twig_SimpleFunction('tinymce_path', array($this, 'renderJsPath'), $options),
            new \Twig_SimpleFunction('tinymce_file_picker_callback', array($this, 'renderFilePicker'), $options),
            new \Twig_SimpleFunction('tinymce_filebrowser_path', array($this, 'renderFilebrowserPath'), $options),
            new \Twig_SimpleFunction('tinymce_filebrowser_type', array($this, 'renderFileBrowserType'), $options),
            new \Twig_SimpleFunction('tinymce_language', array($this, 'renderLanguage'), $options),
            new \Twig_SimpleFunction('tinymce_relative_urls', array($this, 'renderRelativeUrls'), $options),
            new \Twig_SimpleFunction('tinymce_convert_urls', array($this, 'renderConvertUrls'), $options),
            new \Twig_SimpleFunction('tinymce_toolbars', array($this, 'renderToolbars'), $options),
            new \Twig_SimpleFunction('tinymce_plugins', array($this, 'renderPlugins'), $options),
            new \Twig_SimpleFunction('tinymce_theme', array($this, 'renderTheme'), $options),
            new \Twig_SimpleFunction('tinymce_width', array($this, 'renderWidth'), $options),
            new \Twig_SimpleFunction('tinymce_height', array($this, 'renderHeight'), $options),
            new \Twig_SimpleFunction('tinymce_image_advtab', array($this, 'renderImgAdvTab'), $options),
            new \Twig_SimpleFunction('tinymce_menubar', array($this, 'renderMenubar'), $options),
            new \Twig_SimpleFunction('tinymce_templates', array($this, 'renderTemplates'), $options),
            new \Twig_SimpleFunction('tinymce_toolbar_items_size', array($this, 'renderToolbarItemSize'), $options),

        );
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
     * @return string
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
