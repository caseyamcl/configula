<?php

require_once(__DIR__ . '/../Configula/DriverInterface.php');
require_once(__DIR__ . '/../Configula/Drivers/Yaml.php');

class YamlDriverTest extends PHPUnit_Framework_TestCase {

    private $content_path;

    private $good_file_path;
    private $empty_file_path;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();

        $ds = DIRECTORY_SEPARATOR;
        $this->content_path = sys_get_temp_dir() . $ds . 'phpunit_configula_test_' . time();

        //Setup fake content directory
        mkdir($this->content_path);

        $sample_code = '
         a: value
         b:
             [1, 2, 3]
         c:
             d: e
             f: g
             h: i
        ';

        file_put_contents($this->content_path . $ds . 'testconfig.yaml', $sample_code);
        $this->good_file_path = $this->content_path . $ds . 'testconfig.yaml';

        file_put_contents($this->content_path . $ds . 'testempty.yaml', '');
        $this->empty_file_path = $this->content_path . $ds . 'testempty.yaml';    
    }

    // --------------------------------------------------------------

    function tearDown()
    {    
        $ds = DIRECTORY_SEPARATOR;

        unlink($this->empty_file_path);
        unlink($this->good_file_path);
        rmdir($this->content_path);

        parent::tearDown();
    } 

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds() {

        $obj = new Configula\Drivers\Yaml();
        $this->assertInstanceOf('Configula\Drivers\Yaml', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems() {
        $obj = new Configula\Drivers\Yaml();
        $result = $obj->read($this->good_file_path);

        $match_array = array();
        $match_array['a'] = "value";
        $match_array["b"] = array(1, 2, 3);
        $match_array["c"]["d"] = 'e';
        $match_array["c"]["f"] = 'g';
        $match_array["c"]["h"] = 'i';

        $this->assertEquals($match_array, $result);
    }

    // --------------------------------------------------------------

    public function testEmptyFileReturnsEmptyArray() {

        $obj = new Configula\Drivers\Yaml();
        $result = $obj->read($this->empty_file_path);

        $this->assertEquals(array(), $result);  
    }
}

/* EOF: PhpDriverTest.php */
