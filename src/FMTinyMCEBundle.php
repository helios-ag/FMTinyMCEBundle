<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle;

use FM\TinyMCEBundle\DependencyInjection\FMTinyMCEExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class FMTinyMCEBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new FMTinyMCEExtension();
    }
}
