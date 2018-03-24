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
use PHPUnit\Framework\TestCase;

/**
 * JSON Driver Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class JsonDriverTest extends TestCase
{

    private $goodFilePath;
    private $badFilePath;
    private $emptyFilePath;

    function setUp()
    {
        parent::setUp();

        $this->goodFilePath  = realpath(__DIR__ . '/../fixtures/json/config.json');
        $this->badFilePath   = realpath(__DIR__ . '/../fixtures/json/bad.json');
        $this->emptyFilePath = realpath(__DIR__ . '/../fixtures/json/empty.json');
    } 

    public function testInstantiateAsObjectSucceeds()
    {
        $loader = new JsonFileLoader($this->goodFilePath);
        $this->assertInstanceOf(JsonFileLoader::class, $loader);
    }

    public function testGetConfigReturnsCorrectItems()
    {
        $loader = new JsonFileLoader($this->goodFilePath);

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
    public function testBadContentThrowsException()
    {
        $loader = new JsonFileLoader($this->badFilePath);
        $loader->load();
    }

    public function testEmptyContentReturnsEmptyConfig()
    {
        $loader = new JsonFileLoader($this->emptyFilePath);
        $this->assertEquals([], $loader->load()->getArrayCopy());
    }
}
