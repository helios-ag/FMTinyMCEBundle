<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests;

use FM\TinyMCEBundle\FMTinyMCEBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class FMTinyMCEBundleTest extends TestCase
{
    public function testItIsASymfonyBundle(): void
    {
        self::assertInstanceOf(Bundle::class, new FMTinyMCEBundle());
    }
}
