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
 * IniDriverTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class IniFileLoaderTest extends TestCase
{
    private $goodFilePath;
    private $badFilePath;
    private $emptyFilePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->goodFilePath  = realpath(__DIR__ . '/../fixtures/ini/config.ini');
        $this->badFilePath   = realpath(__DIR__ . '/../fixtures/ini/bad.ini');
        $this->emptyFilePath = realpath(__DIR__ . '/../fixtures/ini/empty.ini');
    }

    public function testInstantiateAsObjectSucceeds()
    {
        $loader = new IniFileLoader($this->goodFilePath);
        $this->assertInstanceOf(IniFileLoader::class, $loader);
    }

    public function testGetConfigReturnsCorrectItems()
    {
        $loader = new IniFileLoader($this->goodFilePath);

        $expected['a'] = "value";
        $expected["b"] = [1, 2, 3];
        $expected["c_one"] = 'd';
        $expected["c_two"] = 'e';
        $expected["c_thr"] = 'f';

        $this->assertEquals($expected, $loader->load()->getArrayCopy());
    }

    /**
     * @expectedException \Configula\Exception\ConfigLoaderException
     */
    public function testBadContentThrowsException()
    {
        $loader = new IniFileLoader($this->badFilePath);
        $loader->load();
    }

    public function testEmptyContentReturnsEmptyConfig()
    {
        $loader = new IniFileLoader($this->emptyFilePath);
        $this->assertEquals([], $loader->load()->getArrayCopy());
    }
}

