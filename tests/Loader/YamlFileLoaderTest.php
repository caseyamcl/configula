<?php

/**
 * Configula - A simple configuration tool
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * ------------------------------------------------------------------
 */

namespace Configula\Loader;

/**
 * YAML Driver Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class YamlFileLoaderTest extends \PHPUnit\Framework\TestCase
{
    private $goodFilePath;
    private $emptyFilePath;
    private $badFilePath;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->goodFilePath = realpath(__DIR__ . '/../fixtures/yaml/config.yml');
        $this->emptyFilePath = realpath(__DIR__ . '/../fixtures/yaml/empty.yml');
        $this->badFilePath = realpath(__DIR__ . '/../fixtures/yaml/bad.yml');
    }

    public function testInstantiateAsObjectSucceeds()
    {
        $loader = new YamlFileLoader($this->goodFilePath);
        $this->assertInstanceOf(YamlFileLoader::class, $loader);
    }

    public function testGetConfigReturnsCorrectItems()
    {
        $loader = new YamlFileLoader($this->goodFilePath);

        $expected['a'] = "value";
        $expected["b"] = [1, 2, 3];
        $expected["c"]["d"] = 'e';
        $expected["c"]["f"] = 'g';
        $expected["c"]["h"] = 'i';

        $this->assertEquals($expected, $loader->load()->getArrayCopy());
    }

    /**
     * @expectedException \Configula\Exception\ConfigLoaderException
     */
    public function testBadContentThrowsParseException()
    {
        $loader = new YamlFileLoader($this->badFilePath);
        $loader->load()->getArrayCopy();
    }

    public function testEmptyFileReturnsEmptyConfig()
    {
        $loader = new YamlFileLoader($this->emptyFilePath);
        $this->assertEquals([], $loader->load()->getArrayCopy());
    }
}
