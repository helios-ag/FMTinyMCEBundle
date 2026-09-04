<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\Composer;

use FM\TinyMCEBundle\Composer\TinyMCEAssetInstaller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class TinyMCEAssetInstallerTest extends TestCase
{
    private string $temporaryDirectory;

    protected function setUp(): void
    {
        $this->temporaryDirectory = sys_get_temp_dir().'/fm-tinymce-'.bin2hex(random_bytes(8));
        mkdir($this->temporaryDirectory, 0777, true);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->temporaryDirectory);
    }

    public function testItMirrorsTinyMceAssetsIdempotently(): void
    {
        $source = $this->temporaryDirectory.'/vendor/tinymce/tinymce';
        $destination = $this->temporaryDirectory.'/public/assets/tinymce';
        mkdir($source.'/plugins/link', 0777, true);
        file_put_contents($source.'/tinymce.min.js', 'tinymce');
        file_put_contents($source.'/plugins/link/plugin.min.js', 'link');

        TinyMCEAssetInstaller::copyFromPaths($source, $destination);
        TinyMCEAssetInstaller::copyFromPaths($source, $destination);

        self::assertSame('tinymce', file_get_contents($destination.'/tinymce.min.js'));
        self::assertSame('link', file_get_contents($destination.'/plugins/link/plugin.min.js'));
    }

    public function testItRejectsAMissingTinyMceSource(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('tinymce.min.js');

        TinyMCEAssetInstaller::copyFromPaths($this->temporaryDirectory.'/missing', $this->temporaryDirectory.'/public/assets/tinymce');
    }
}
