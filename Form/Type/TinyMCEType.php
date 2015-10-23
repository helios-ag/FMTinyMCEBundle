<?php

namespace FM\TinyMCEBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TinyMCEType
 * @package FM\TinyMCEBundle\Form\Type
 */
class TinyMCEType extends AbstractType
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var boolean
     */
    private $enable = true;

    /**
     * @var boolean
     */
    private $inline = false;

    /**
     * TinyMCEType constructor.
     * @param $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets/Checks if the widget is enabled.
     *
     * @param boolean|null $enable TRUE if the widget is enabled else FALSE.
     *
     * @return boolean TRUE if the widget is enabled else FALSE.
     */
    public function isEnable($enable = null)
    {
        if ($enable !== null) {
            $this->enable = (bool) $enable;
        }
        return $this->enable;
    }

    /**
     * @param null $inline
     * @return bool
     */
    public function isInline($inline = null)
    {
        if ($inline !== null) {
            $this->inline = (bool) $inline;
        }
        return $this->inline;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('inline', $options['inline']);
            $builder->setAttribute('base_path', $options['base_path']);
            $builder->setAttribute('instance', $options['instance']);
//            $builder->setAttribute('filebrowser_type', $options['filebrowser_type']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enable'] = $form->getConfig()->getAttribute('enable');
        if ($form->getConfig()->getAttribute('enable')) {
            $view->vars['inline']     = $form->getConfig()->getAttribute('inline');
            $view->vars['base_path']  = $form->getConfig()->getAttribute('base_path');
            $view->vars['instance']   = $form->getConfig()->getAttribute('instance');
//            $view->vars['filebrowser_type'] = $form->getConfig()->getAttribute('filebrowser_type');
            $view->vars['plugins']    = $form->getConfig()->getAttribute('plugins');
            $view->vars['styles']     = $form->getConfig()->getAttribute('styles');
            $view->vars['templates']  = $form->getConfig()->getAttribute('templates');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'enable'      => true,
                'inline'      => $this->inline,
                'base_path'   => $this->parameters['base_path'],
                'instance'    => '',
//                'filebrowser_type' => $this->parameters['filebrowser_type'],
                'plugins'     => array(),
                'styles'      => array(),
                'templates'   => array(),
            ))
        ;
        $allowedTypesMap = array(
            'enable'      => 'bool',
            'inline'      => 'bool',
            'base_path'   => 'string',
            'instance'    => 'string',
//            'filebrowser_type' => 'string',
            'plugins'     => 'array',
            'styles'      => 'array',
            'templates'   => 'array',

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
}
