<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\Functional;

use FM\TinyMCEBundle\Configuration\TinyMCEConfigurationBuilder;
use FM\TinyMCEBundle\Form\Type\TinyMCEType;
use FM\TinyMCEBundle\Tests\AppKernel;
use FM\TinyMCEBundle\Twig\TinyMCEExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ContainerCompilationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    public function testTheModernServicesCompileWithoutSymfonyTemplating(): void
    {
        self::bootKernel();

        self::assertTrue(self::getContainer()->has(TinyMCEType::class));
        self::assertTrue(self::getContainer()->has(TinyMCEExtension::class));
        self::assertTrue(self::getContainer()->has(TinyMCEConfigurationBuilder::class));
    }
}
