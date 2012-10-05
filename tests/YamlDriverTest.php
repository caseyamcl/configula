<?php

class YamlDriverTest extends PHPUnit_Framework_TestCase
{
    private $goodFilePath;
    private $emptyFilePath;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();
        
        $this->goodFilePath = realpath(__DIR__ . '/fixtures/yaml/config.yml');
        $this->emptyFilePath = realpath(__DIR__ . '/fixtures/yaml/empty.yml');
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

    public function testEmptyFileReturnsEmptyArray()
    {
        $obj = new Configula\Drivers\Yaml();
        $result = $obj->read($this->emptyFilePath);

        $this->assertEquals(array(), $result);  
    }
}

/* EOF: PhpDriverTest.php */
