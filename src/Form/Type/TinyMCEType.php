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
    private $basePath = 'assets/tinymce/';

    /**
     * @var string
     */
    private $jsPath = 'assets/tinymce/tinymce.min.js';

    /**
     * @param bool $enable
     *
     * @return bool
     */
    public function isEnabled($enable = true)
    {
        if (null !== $enable) {
            $this->enable = (bool) $enable;
        }

        return $this->enable;
    }

    public function isInline($inline = null)
    {
        if (null !== $inline) {
            $this->inline = (bool) $inline;
        }

        return $this->inline;
    }

    /**
     * TinyMCEType constructor.
     */
    public function __construct(array $parameters = [])
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
            ->setDefaults([
                'enable'      => $this->enable,
                'inline'      => $this->inline,
                'base_path'   => $this->basePath,
                'js_path'     => $this->jsPath,
                'instance'    => 'default',
            ])
        ;
        $allowedTypesMap = [
            'enable'      => 'bool',
            'inline'      => 'bool',
            'base_path'   => 'string',
            'js_path'     => 'string',
            'instance'    => 'string',
        ];
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
     * {@inheritdoc}
     */
    public function getParent()
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Symfony\Component\Form\Extension\Core\Type\TextareaType';
        }

        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
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
