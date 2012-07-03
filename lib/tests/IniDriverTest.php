<?php

require_once(__DIR__ . '/../Configula/DriverInterface.php');
require_once(__DIR__ . '/../Configula/Drivers/Ini.php');

class IniDriverTest extends PHPUnit_Framework_TestCase {

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
            a = value

            b[] = 1
            b[] = 2
            b[] = 3

            c_one = d
            c_two = e
            c_thr = f
        ';

        file_put_contents($this->content_path . $ds . 'testconfig.ini', $sample_code);
        $this->file_path = $this->content_path . $ds . 'testconfig.ini';

        $bad_code = '
            asdf1239423-497y8-398289--83--@#_#@*_#*_
        ';

        file_put_contents($this->content_path . $ds . 'testbad.ini', $bad_code);
        $this->bad_file_path = $this->content_path . $ds . 'testbad.ini';

        file_put_contents($this->content_path . $ds . 'testempty.ini', '');
        $this->empty_file_path = $this->content_path . $ds . 'testempty.ini';       
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

        $obj = new Configula\Drivers\Ini();
        $this->assertInstanceOf('Configula\Drivers\Ini', $obj);
    }

    // --------------------------------------------------------------

    public function testGetConfigReturnsCorrectItems() {
        $obj = new Configula\Drivers\Ini();
        $result = $obj->read($this->file_path);

        $match_array = array();
        $match_array['a'] = "value";
        $match_array["b"] = array(1, 2, 3);
        $match_array["c_one"] = 'd';
        $match_array["c_two"] = 'e';
        $match_array["c_thr"] = 'f';

        $this->assertEquals($match_array, $result);
    }

    // --------------------------------------------------------------

    public function testBadContentReturnsEmptyArray() {
        $obj = new Configula\Drivers\Ini();
        $result = $obj->read($this->bad_file_path);

        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testEmptyContentReturnsEmptyArray() {

        $obj = new Configula\Drivers\Ini();
        $result = $obj->read($this->empty_file_path);

        $this->assertEquals(array(), $result);
    }
}

/* EOF: PhpDriverTest.php */
