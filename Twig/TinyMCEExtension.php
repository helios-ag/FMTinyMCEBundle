<?php

namespace FM\TinyMCEBundle\Twig;

use FM\TinyMCEBundle\Templating\TinyMCEHelper;

/**
 * Class TinyMCEExtenion
 * @package FM\TinyMCEBundle\Twig
 */
class TinyMCEExtension extends \Twig_Extension
{
    /**
     * @var TinyMCEHelper
     */
    private $helper;

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
            new \Twig_SimpleFunction('tinymce_base_path', array($this, 'renderBasePath'), $options),
            new \Twig_SimpleFunction('tinymce_filebrowser_callback', array($this, 'renderFilebrowser'), $options),
            new \Twig_SimpleFunction('tinymce_filebrowser_type', array($this, 'renderFileBrowserType'), $options),
            new \Twig_SimpleFunction('tinymce_toolbars', array($this, 'renderToolbars'), $options),
            new \Twig_SimpleFunction('tinymce_plugins', array($this, 'renderPlugins'), $options),
            new \Twig_SimpleFunction('tinymce_theme', array($this, 'renderTheme'), $options),
            new \Twig_SimpleFunction('tinymce_width', array($this, 'renderWidth'), $options),
            new \Twig_SimpleFunction('tinymce_height', array($this, 'renderHeight'), $options),
            new \Twig_SimpleFunction('tinymce_image_advtab', array($this, 'renderImgAdvTab'), $options),
            new \Twig_SimpleFunction('tinymce_menubar', array($this, 'renderMenubar'), $options),
            new \Twig_SimpleFunction('tinymce_templates', array($this, 'renderTemplates'), $options),
            new \Twig_SimpleFunction('tinymce_toolbar_items_size', array($this, 'renderToolbarItemSize'), $options),
            new \Twig_SimpleFunction('tinymce_filebrowser_path', array($this, 'renderFilebrowserPath'), $options),
        );
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderFileBrowserType($instance)
    {
        return $this->helper->renderFileBrowserType($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderFilebrowserPath($instance)
    {
        return $this->helper->fileBrowserPathHelper($instance);
    }

    /**
     * @param $instance
     * @return mixed
     */
    public function renderToolbarItemSize($instance)
    {
        return $this->helper->getToolbarItemSize($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderTemplates($instance)
    {
        return $this->helper->getTemplates($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderTheme($instance)
    {
        return $this->helper->getTheme($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderImgAdvTab($instance)
    {
        return $this->helper->getImgAdvTab($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderMenubar($instance)
    {
        return $this->helper->getMenubar($instance);
    }

    /**
     * @param $instance
     * @return mixed
     */
    public function renderWidth($instance)
    {
        return $this->helper->getWidth($instance);
    }

    /**
     * @param $instance
     * @return mixed
     */
    public function renderHeight($instance)
    {
        return $this->helper->getHeight($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderFilebrowser($instance)
    {
        return $this->helper->renderFilebrowser($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderToolbars($instance)
    {
        return $this->helper->getToolbars($instance);
    }

    /**
     * @param $instance
     * @return string
     */
    public function renderPlugins($instance)
    {
        return $this->helper->getPlugins($instance);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function renderBasePath($path)
    {
        return $this->helper->renderBasePath($path);
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