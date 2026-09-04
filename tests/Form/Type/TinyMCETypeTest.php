<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\Form\Type;

use FM\TinyMCEBundle\Configuration\InstanceConfigurationResolver;
use FM\TinyMCEBundle\Form\Type\TinyMCEType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class TinyMCETypeTest extends TypeTestCase
{
    protected function getTypes(): array
    {
        return [new TinyMCEType(
            new InstanceConfigurationResolver([
                'default' => ['enabled' => true, 'inline' => false, 'options' => []],
                'inline' => ['enabled' => true, 'inline' => true, 'options' => []],
            ]),
            'assets/tinymce/tinymce.min.js',
        )];
    }

    public function testItExposesTheResolvedProfileToTheView(): void
    {
        $view = $this->factory->create(TinyMCEType::class, null, ['instance' => 'inline'])->createView();

        self::assertTrue($view->vars['tiny_mce_enabled']);
        self::assertTrue($view->vars['tiny_mce_inline']);
        self::assertSame('inline', $view->vars['tiny_mce_instance']);
        self::assertSame('assets/tinymce/tinymce.min.js', $view->vars['tiny_mce_script_path']);
    }

    public function testFieldEnabledOptionOverridesTheProfile(): void
    {
        $view = $this->factory->create(TinyMCEType::class, null, ['enabled' => false])->createView();

        self::assertFalse($view->vars['tiny_mce_enabled']);
    }

    public function testUnknownProfileIsAnInvalidFormOption(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('Unknown TinyMCE instance "missing"');

        $this->factory->create(TinyMCEType::class, null, ['instance' => 'missing'])->createView();
    }

    public function testItUsesTextareaAsItsParent(): void
    {
        self::assertSame(TextareaType::class, (new TinyMCEType(
            new InstanceConfigurationResolver(['default' => ['enabled' => true, 'inline' => false, 'options' => []]]),
            'assets/tinymce/tinymce.min.js',
        ))->getParent());
    }

    public function testItUsesTheRegisteredTinymceFormThemeBlock(): void
    {
        self::assertSame('tinymce', (new TinyMCEType(
            new InstanceConfigurationResolver(['default' => ['enabled' => true, 'inline' => false, 'options' => []]]),
            'assets/tinymce/tinymce.min.js',
        ))->getBlockPrefix());
    }
}
