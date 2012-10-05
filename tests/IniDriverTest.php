<?php

/**
 * Configula INI Driver Class Unit Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * @subpackage Unit Tests
 */

class IniDriverTest extends PHPUnit_Framework_TestCase {

    private $content_path;

    private $goodFilePath;
    private $badFilePath;
    private $emptyFilePath;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();

        $this->goodFilePath = realpath(__DIR__ . '/fixtures/ini/config.ini');
        $this->badFilePath = realpath(__DIR__ . '/fixtures/ini/bad.ini');
        $this->emptyFilePath = realpath(__DIR__ . '/fixtures/ini/empty.ini');
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = new Configula\Drivers\Ini();
        $this->assertInstanceOf('Configula\Drivers\Ini', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems()
    {
        $obj = new Configula\Drivers\Ini();
        $result = $obj->read($this->goodFilePath);

        $match_array = array();
        $match_array['a'] = "value";
        $match_array["b"] = array(1, 2, 3);
        $match_array["c_one"] = 'd';
        $match_array["c_two"] = 'e';
        $match_array["c_thr"] = 'f';

        $this->assertEquals($match_array, $result);
    }

    // --------------------------------------------------------------

    public function testBadContentReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Ini();
        $result = $obj->read($this->badFilePath);

        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testEmptyContentReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Ini();
        $result = $obj->read($this->emptyFilePath);

        $this->assertEquals(array(), $result);
    }
}

/* EOF: PhpDriverTest.php */
