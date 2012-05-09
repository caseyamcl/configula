<?php

require_once(__DIR__ . '/../DriverInterface.php');
require_once(__DIR__ . '/../Drivers/Yaml.php');

class YamlDriverTest extends PHPUnit_Framework_TestCase {

  private $content_path;

  private $file_path;

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
    $this->file_path = $this->content_path . $ds . 'testconfig.yaml';
  }

  // --------------------------------------------------------------

  function tearDown()
  {    
    $ds = DIRECTORY_SEPARATOR;

    unlink($this->file_path);
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
    $result = $obj->read($this->file_path);

    $match_array = array();
    $match_array['a'] = "value";
    $match_array["b"] = array(1, 2, 3);
    $match_array["c"]["d"] = 'e';
    $match_array["c"]["f"] = 'g';
    $match_array["c"]["h"] = 'i';

    $this->assertEquals($match_array, $result);
  }

}

/* EOF: PhpDriverTest.php */