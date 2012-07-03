<?php

require_once(__DIR__ . '/../Configula/DriverInterface.php');
require_once(__DIR__ . '/../Configula/Drivers/Json.php');

class JsonDriverTest extends PHPUnit_Framework_TestCase {

    private $content_path;

    private $file_path;
    private $bad_file_path;
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
        {
            "a": "value",
            "b": [1, 2, 3],
            "c": {"d": "e", "f": "g", "h": "i"}
        }
        ';

        file_put_contents($this->content_path . $ds . 'testconfig.json', $sample_code);
        $this->file_path = $this->content_path . $ds . 'testconfig.json';

        $bad_code = '
            asdf1239423-497y8-398289--83--@#_#@*_#*_
        ';

        file_put_contents($this->content_path . $ds . 'testbad.json', $bad_code);
        $this->bad_file_path = $this->content_path . $ds . 'testbad.json';

        file_put_contents($this->content_path . $ds . 'testempty.json', '');
        $this->empty_file_path = $this->content_path . $ds . 'testempty.json';           
    }

    // --------------------------------------------------------------

    function tearDown()
    {    
        $ds = DIRECTORY_SEPARATOR;

        unlink($this->empty_file_path);
        unlink($this->bad_file_path);
        unlink($this->file_path);
        rmdir($this->content_path);

        parent::tearDown();
    } 

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds() {

        $obj = new Configula\Drivers\Json();
        $this->assertInstanceOf('Configula\Drivers\Json', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems() {
        $obj = new Configula\Drivers\Json();
        $result = $obj->read($this->file_path);

        $match_array = array();
        $match_array['a'] = "value";
        $match_array["b"] = array(1, 2, 3);
        $match_array["c"]["d"] = 'e';
        $match_array["c"]["f"] = 'g';
        $match_array["c"]["h"] = 'i';

        $this->assertEquals($match_array, $result);
    }

    // --------------------------------------------------------------

 public function testBadContentReturnsEmptyArray() {
        $obj = new Configula\Drivers\Json();
        $result = $obj->read($this->bad_file_path);

        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testEmptyContentReturnsEmptyArray() {

        $obj = new Configula\Drivers\Json();
        $result = $obj->read($this->empty_file_path);

        $this->assertEquals(array(), $result);
    }
}

/* EOF: PhpDriverTest.php */
