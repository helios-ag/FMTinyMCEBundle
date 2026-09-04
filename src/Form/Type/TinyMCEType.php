<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Form\Type;

use FM\TinyMCEBundle\Configuration\InstanceConfigurationResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TinyMCEType extends AbstractType
{
    public function __construct(
        private readonly InstanceConfigurationResolver $instances,
        private readonly string $scriptPath,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'enabled' => null,
            'instance' => 'default',
        ]);
        $resolver->setAllowedTypes('enabled', ['null', 'bool']);
        $resolver->setAllowedTypes('instance', 'string');
    }

    /** @param array<string, mixed> $options */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!is_string($options['instance']) || (null !== $options['enabled'] && !is_bool($options['enabled']))) {
            throw new \LogicException('TinyMCE form options were not normalized.');
        }

        try {
            $instance = $this->instances->resolve($options['instance'], $options['enabled']);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidOptionsException($exception->getMessage(), previous: $exception);
        }

        $view->vars['tiny_mce_enabled'] = (bool) $instance['enabled'];
        $view->vars['tiny_mce_instance'] = $options['instance'];
        $view->vars['tiny_mce_inline'] = (bool) $instance['inline'];
        $view->vars['tiny_mce_script_path'] = $this->scriptPath;
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
