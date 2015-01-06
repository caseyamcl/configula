<?php

/**
 * Configula - A simple configuration tool
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * ------------------------------------------------------------------
 */

/**
 * PHP Driver Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PhpDriverTest extends PHPUnit_Framework_TestCase {

    private $content_path;

    private $goodFilePath;
    private $badFilePath;
    private $emptyFilePath;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();

        $this->goodFilePath = realpath(__DIR__ . '/fixtures/php/config.php');
        $this->badFilePath = realpath(__DIR__ . '/fixtures/php/bad.php');
        $this->emptyFilePath = realpath(__DIR__ . '/fixtures/php/empty.php');
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = new Configula\Drivers\Php();
        $this->assertInstanceOf('Configula\Drivers\Php', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems()
    {
        $obj = new Configula\Drivers\Php();
        $result = $obj->read($this->goodFilePath);

        $config = array();
        $config["a"] = "value";
        $config["b"] = array(1, 2, 3);
        $config["c"] = (object) array("d", "e", "f");

        $this->assertEquals($config, $result);
    }

    // --------------------------------------------------------------

    public function testBadContentReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Php();
        $result = $obj->read($this->badFilePath);

        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testEmptyContentReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Php();
        $result = $obj->read($this->emptyFilePath);

        $this->assertEquals(array(), $result);
    }
}

/* EOF: PhpDriverTest.php */
