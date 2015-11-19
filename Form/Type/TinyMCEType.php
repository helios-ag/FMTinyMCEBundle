<?php

namespace FM\TinyMCEBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TinyMCEType.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2015- Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class TinyMCEType extends AbstractType
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @var bool
     */
    private $enable;

    /**
     * @var bool
     */
    private $inline;

    /**
     * @var string
     */
    private $basePath = 'bundles/fmtinymce/';

    /**
     * @var string
     */
    private $jsPath = 'bundle/fmtinymce/tinymce.min.js';

    /**
     * @param bool $enable
     *
     * @return bool
     */
    public function isEnabled($enable = true)
    {
        if ($enable !== null) {
            $this->enable = (bool) $enable;
        }

        return $this->enable;
    }

    public function isInline($inline = null)
    {
        if ($inline !== null) {
            $this->inline = (bool) $inline;
        }

        return $this->inline;
    }

    /**
     * TinyMCEType constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
        $this->inline     = $parameters['inline'];
        $this->enable     = $parameters['enable'];
        $this->setBasePath($parameters['base_path']);
        $this->setJsPath($parameters['js_path']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('base_path', $options['base_path']);
            $builder->setAttribute('js_path', $options['js_path']);
            $builder->setAttribute('inline', $options['inline']);
            $builder->setAttribute('instance', $options['instance']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enable'] = $form->getConfig()->getAttribute('enable');
        if ($form->getConfig()->getAttribute('enable')) {
            $view->vars['base_path'] = $form->getConfig()->getAttribute('base_path');
            $view->vars['inline']    = $form->getConfig()->getAttribute('inline');
            $view->vars['js_path']   = $form->getConfig()->getAttribute('js_path');
            $view->vars['instance']  = $form->getConfig()->getAttribute('instance');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'enable'      => $this->enable,
                'inline'      => $this->inline,
                'base_path'   => $this->basePath,
                'js_path'     => $this->jsPath,
                'instance'    => 'default',
            ))
        ;
        $allowedTypesMap = array(
            'enable'      => 'bool',
            'inline'      => 'bool',
            'base_path'   => 'string',
            'js_path'     => 'string',
            'instance'    => 'string',

        );
        if (Kernel::VERSION_ID >= 20600) {
            foreach ($allowedTypesMap as $option => $allowedTypes) {
                $resolver->addAllowedTypes($option, $allowedTypes);
            }
        } else {
            $resolver->addAllowedTypes($allowedTypesMap);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tinymce';
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * @param string $jsPath
     */
    public function setJsPath($jsPath)
    {
        $this->jsPath = $jsPath;
    }
}
