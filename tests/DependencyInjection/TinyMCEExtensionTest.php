<?php

namespace FM\TinyMCEBundle\Tests\DependencyInjection;

use FM\TinyMCEBundle\DependencyInjection\FMTinyMCEExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Class TinyMCEExtensionTest
 */
class TinyMCEExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new FMTinyMCEExtension(),
        );
    }

    public function testServices()
    {
        $this->load();
        $this->assertContainerBuilderHasAlias('fm_tinymce.form.type');
        $this->assertContainerBuilderHasService('fm_tinymce.templating.helper');
    }

    public function testMinimumConfiguration()
    {
        $this->container = new ContainerBuilder();
        $loader = new FMTinyMCEExtension();
        $loader->load(array($this->getMinimalConfiguration()), $this->container);
        $this->assertTrue($this->container instanceof ContainerBuilder);
    }

    protected function getMinimalConfiguration()
    {
        $yaml = <<<'EOF'
instances:
    first_instance:
        language: en_US
        width: 300
        height: 400
    my_advanced_configuration:
        language: ru_RU
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
