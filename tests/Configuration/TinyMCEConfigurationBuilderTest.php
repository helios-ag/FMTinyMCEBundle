<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\Configuration;

use FM\TinyMCEBundle\Configuration\InstanceConfigurationResolver;
use FM\TinyMCEBundle\Configuration\TinyMCEConfigurationBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

final class TinyMCEConfigurationBuilderTest extends TestCase
{
    public function testItBuildsBundleOwnedConfigurationAfterProfileOptions(): void
    {
        $builder = new TinyMCEConfigurationBuilder(
            new InstanceConfigurationResolver([
                'default' => [
                    'enabled' => true,
                    'inline' => false,
                    'options' => [
                        'license_key' => 'unsafe',
                        'selector' => '#unsafe',
                        'toolbar' => 'bold',
                    ],
                ],
            ]),
            new Packages(new Package(new EmptyVersionStrategy())),
            'assets/tinymce',
        );

        self::assertSame([
            'toolbar' => 'bold',
            'base_url' => 'assets/tinymce',
            'inline' => false,
            'license_key' => 'gpl',
            'selector' => '#fixture_body',
        ], $builder->build('fixture_body', 'default'));
    }

    public function testItEncodesScriptSensitiveCharactersSafely(): void
    {
        $builder = new TinyMCEConfigurationBuilder(
            new InstanceConfigurationResolver(['default' => ['enabled' => true, 'inline' => false, 'options' => []]]),
            new Packages(new Package(new EmptyVersionStrategy())),
            'assets/tinymce',
        );

        $json = $builder->encodeForScript(['content_style' => '</script><script>window.pwned=1</script>']);

        self::assertStringContainsString('\\u003C', $json);
        self::assertStringNotContainsString('</script>', $json);
        self::assertSame('</script><script>window.pwned=1</script>', json_decode($json, true, flags: JSON_THROW_ON_ERROR)['content_style']);
    }
}
