<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Tests\FilePicker;

use FM\TinyMCEBundle\FilePicker\ElfinderFilePicker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ElfinderFilePickerTest extends TestCase
{
    public function testItBuildsTheConfiguredElfinderUrl(): void
    {
        $urls = $this->createMock(UrlGeneratorInterface::class);
        $urls->expects(self::once())
            ->method('generate')
            ->with('elfinder', ['instance' => 'tinymce'], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/elfinder?instance=tinymce');

        $picker = new ElfinderFilePicker($urls);

        self::assertSame(
            ['fm_elfinder_url' => '/elfinder?instance=tinymce'],
            $picker->build(['type' => 'fm_elfinder', 'route' => 'elfinder', 'route_parameters' => ['instance' => 'tinymce']], 'default'),
        );
    }

    public function testItRejectsAnElfinderPickerWithoutARoute(): void
    {
        $picker = new ElfinderFilePicker($this->createMock(UrlGeneratorInterface::class));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('fm_tinymce.instances.default.file_picker.route');

        $picker->build(['type' => 'fm_elfinder', 'route' => null, 'route_parameters' => []], 'default');
    }
}
