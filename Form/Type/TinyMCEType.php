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
     * TinyMCEType constructor.
     * @param $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('base_path', $options['base_path']);
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
            $view->vars['base_path']  = $form->getConfig()->getAttribute('base_path');
            $view->vars['instance']   = $form->getConfig()->getAttribute('instance');

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
                'base_path'   => $this->parameters['base_path'],
                'instance'    => '',
            ))
        ;
        $allowedTypesMap = array(
            'enable'      => 'bool',
            'base_path'   => 'string',
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
}
