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
 * PHP Driver Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PhpFileLoaderTest extends TestCase
{

    private $goodFilePath;
    private $badFilePath;
    private $emptyFilePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->goodFilePath  = realpath(__DIR__ . '/../fixtures/php/config.php');
        $this->badFilePath   = realpath(__DIR__ . '/../fixtures/php/bad.php');
        $this->emptyFilePath = realpath(__DIR__ . '/../fixtures/php/empty.php');
    }

    public function testInstantiateAsObjectSucceeds()
    {
        $loader = new PhpFileLoader($this->goodFilePath);
        $this->assertInstanceOf(PhpFileLoader::class, $loader);
    }

    public function testGetConfigReturnsCorrectItems()
    {
        $loader = new PhpFileLoader($this->goodFilePath);

        $expected["a"] = "value";
        $expected["b"] = array(1, 2, 3);
        $expected["c"] = (object) ["d", "e", "f"];

        $this->assertEquals($expected, $loader->load()->getArrayCopy());
    }

    /**
     * @expectedException \Configula\Exception\ConfigLoaderException
     */
    public function testBadContentThrowsException()
    {
        $loader = new PhpFileLoader($this->badFilePath);
        $loader->load();
    }

    public function testEmptyContentReturnsEmptyConfig()
    {
        $loader = new PhpFileLoader($this->emptyFilePath);
        $this->assertEquals([], $loader->load()->getArrayCopy());
    }
}

