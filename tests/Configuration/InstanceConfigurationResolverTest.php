<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\Configuration;

use FM\TinyMCEBundle\Configuration\InstanceConfigurationResolver;
use PHPUnit\Framework\TestCase;

final class InstanceConfigurationResolverTest extends TestCase
{
    public function testItSelectsTheNamedProfileAndAppliesAFieldOverride(): void
    {
        $resolver = new InstanceConfigurationResolver([
            'default' => ['enabled' => true, 'options' => ['toolbar' => 'bold']],
            'compact' => ['enabled' => true, 'options' => ['toolbar' => 'italic']],
        ]);

        self::assertSame([
            'enabled' => false,
            'options' => ['toolbar' => 'italic'],
        ], $resolver->resolve('compact', false));
    }

    public function testItListsAvailableProfilesForAnUnknownProfile(): void
    {
        $resolver = new InstanceConfigurationResolver([
            'default' => ['enabled' => true],
            'compact' => ['enabled' => true],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown TinyMCE instance "missing". Available instances: compact, default.');

        $resolver->resolve('missing');
    }
}
