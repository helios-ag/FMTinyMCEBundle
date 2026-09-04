<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

final class TinyMCEAssetInstaller
{
    public static function copy(Event $event): void
    {
        $composer = $event->getComposer();
        $vendorDirectory = $composer->getConfig()->get('vendor-dir');
        $extra = $composer->getPackage()->getExtra();

        if (!is_string($vendorDirectory)) {
            throw new \RuntimeException('Composer vendor-dir must be a string.');
        }

        $destination = $extra['tinymce-dir'] ?? 'public/assets/tinymce';
        if (!is_string($destination)) {
            throw new \RuntimeException('The Composer extra.tinymce-dir value must be a string.');
        }

        self::copyFromPaths($vendorDirectory.'/tinymce/tinymce', $destination);
        $event->getIO()->write(sprintf('Copied TinyMCE assets to %s.', $destination));
    }

    public static function copyFromPaths(string $source, string $destination): void
    {
        if (!is_file($source.'/tinymce.min.js')) {
            throw new \RuntimeException(sprintf('TinyMCE source is missing tinymce.min.js: %s.', $source));
        }

        $filesystem = new Filesystem();
        $filesystem->mkdir($destination);
        $filesystem->mirror($source, $destination, null, ['override' => true, 'delete' => false]);
    }
}
