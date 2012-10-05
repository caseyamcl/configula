<?php

/**
 * Configula JSON Driver Class Unit Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * @subpackage Unit Tests
 */

class JsonDriverTest extends PHPUnit_Framework_TestCase {

    private $content_path;

    private $goodFilePath;
    private $badFilePath;
    private $emptyFilePath;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();

        $this->goodFilePath = realpath(__DIR__ . '/fixtures/json/config.json');
        $this->badFilePath = realpath(__DIR__ . '/fixtures/json/bad.json');
        $this->emptyFilePath = realpath(__DIR__ . '/fixtures/json/empty.json');
    } 

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = new Configula\Drivers\Json();
        $this->assertInstanceOf('Configula\Drivers\Json', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems()
    {
        $obj = new Configula\Drivers\Json();
        $result = $obj->read($this->goodFilePath);

        $match_array = array();
        $match_array['a'] = "value";
        $match_array["b"] = array(1, 2, 3);
        $match_array["c"]["d"] = 'e';
        $match_array["c"]["f"] = 'g';
        $match_array["c"]["h"] = 'i';

        $this->assertEquals($match_array, $result);
    }

    // --------------------------------------------------------------

    public function testBadContentReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Json();
        $result = $obj->read($this->badFilePath);

        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testEmptyContentReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Json();
        $result = $obj->read($this->emptyFilePath);

        $this->assertEquals(array(), $result);
    }
}

/* EOF: PhpDriverTest.php */
