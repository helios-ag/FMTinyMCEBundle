<?php

namespace FM\TinyMCEBundle\Tests\Templating;

use DirectoryIterator;
use FM\TinyMCEBundle\Templating\TinyMCEHelper;
use Symfony\Component\Yaml\Parser;

/**
 * Class TinyMCEHelperTest.
 */
class TinyMCEHelperTest extends \PHPUnit\Framework\TestCase
{
    /** @var \FM\TinyMCEBundle\Templating\TinyMCEHelper */
    private $helper;

    /** @var \Symfony\Component\Asset\Packages|\Symfony\Component\Templating\Helper\CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $assetsHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $routerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        if (class_exists('Symfony\Component\Asset\Packages')) {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Asset\Packages')
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
                ->disableOriginalConstructor()
                ->getMock();
        }
        $this->routerMock = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->getMock();

        $parser = new Parser();
        $params = $parser->parse(file_get_contents(__DIR__.'/../Fixtures/config/DI/default.yml'));
        $this->helper = new TinyMCEHelper($params['fm_tinymce']['instances'], $this->routerMock, $this->assetsHelperMock);
    }

    protected function tearDown(): void
    {
        unset($this->assetsHelperMock);
        unset($this->routerMock);
    }

    /**
     * Provides params array from yaml files.
     *
     * @return array
     */
    public function paramsProvider()
    {
        $parser = new Parser();
        $data = [];

        foreach (new DirectoryIterator(__DIR__.'/../Fixtures/config/DI/') as $file) {
            if ($file->isFile()) {
                $data[$file->getFilename()] = $parser->parse($file->getFilename());
            }
        }

        return $data;
    }

    /**
     * Gets the url.
     *
     * @return array the url
     */
    public function pathProvider()
    {
        return [
            ['path', 'url', 'url'],
            ['path', 'url?v=2', 'url'],
        ];
    }

    /**
     * Gets the language.
     *
     * @return array the language
     */
    public function languageProvider()
    {
        return [
            ['ru', 'ru'],
            ['en_US', 'en-us'],
        ];
    }

    /**
     * @dataProvider pathProvider
     *
     * @param $path
     * @param $asset
     * @param $url
     */
    public function testGetBasePath($path, $asset, $url)
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->willReturn($asset);

        $this->assertSame($url, $this->helper->getBasePath($path));
    }

    public function testGetJsPath()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('foo'))
            ->willReturn('bar');

        $this->assertSame('bar', $this->helper->getJsPath('foo'));
    }

    /**
     * Tests file_picker_callback.
     */
    public function testGetFilePickerCallback()
    {
        $this->assertSame('file_picker_callback: elFinderBrowser,', $this->helper->getFilePickerCallback());
    }

    public function testGetFileBrowserPathHelper()
    {
        $this->routerMock
            ->expects($this->once())
            ->method('generate')
            ->willReturn('bar');

        $this->assertSame('bar', $this->helper->getFileBrowserPathHelper());
    }

    public function testGetLanguage()
    {
        $this->assertSame('language: "en_us",', $this->helper->getLanguage());
    }

    public function testGetToolbars()
    {
        $this->assertSame('toolbar1: "foo",toolbar2: "bar",', $this->helper->getToolbars());
    }

    public function testToolbarItemSize()
    {
        $this->assertSame('toolbar_items_size: "small",', $this->helper->getToolbarItemSize());
    }

    public function testGetTheme()
    {
        $this->assertSame('theme: "modern",', $this->helper->getTheme());
    }

    public function testGetPlugins()
    {
        $this->assertSame('plugins: [foo],', $this->helper->getPlugins());
    }

    public function testWidth()
    {
        $this->assertSame('width: 600,', $this->helper->getWidth());
    }

    public function testHeight()
    {
        $this->assertSame('height: 300,', $this->helper->getHeight());
    }

    public function testAdvTab()
    {
        $this->assertSame('image_advtab: false,', $this->helper->getImgAdvTab());
    }

    public function testGetMenubar()
    {
        $this->assertSame('menubar: "file",', $this->helper->getMenubar());
    }

    public function testGetFileBrowserType()
    {
        $this->assertSame('fm_elfinder', $this->helper->getFileBrowserType());
    }

    public function testGetConvertedUrls()
    {
        $this->assertSame('convert_urls: false,', $this->helper->getConvertUrls());
    }

    public function testGetRelativeUrls()
    {
        $this->assertSame('relative_urls: false,', $this->helper->getRelativeUrls());
    }
}
