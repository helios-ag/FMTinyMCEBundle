<?php
/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace FM\TinyMCEBundle\Tests\Fixtures\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Fixtures framework extension.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FrameworkExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('templating.engines', ['twig']);
        $container->setParameter('templating.helper.form.resources', []);
        $container->setParameter('twig.form.resources', []);
    }
}
