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
 * YAML Driver Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class YamlDriverTest extends PHPUnit_Framework_TestCase
{
    private $goodFilePath;
    private $emptyFilePath;
    private $badFilePath;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();
        
        $this->goodFilePath = realpath(__DIR__ . '/fixtures/yaml/config.yml');
        $this->emptyFilePath = realpath(__DIR__ . '/fixtures/yaml/empty.yml');
        $this->badFilePath = realpath(__DIR__ . '/fixtures/yaml/bad.yml');
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {

        $obj = new Configula\Drivers\Yaml();
        $this->assertInstanceOf('Configula\Drivers\Yaml', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems()
    {
        $obj = new Configula\Drivers\Yaml();
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
        $obj = new Configula\Drivers\Yaml();
        $result = $obj->read($this->badFilePath);

        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testEmptyFileReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Yaml();
        $result = $obj->read($this->emptyFilePath);

        $this->assertEquals(array(), $result);  
    }
}

/* EOF: PhpDriverTest.php */
