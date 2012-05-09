<?php

require_once(__DIR__ . '/../DriverInterface.php');
require_once(__DIR__ . '/../Drivers/Php.php');

class PhpDriverTest extends PHPUnit_Framework_TestCase {

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
      $config = array();
      $config["a"] = "value";
      $config["b"] = array(1, 2, 3);
      $config["c"] = (object) array("d", "e", "f");
    ';

    file_put_contents($this->content_path . $ds . 'testconfig.php', "<?php\n\n" . $sample_code . "\n/*EOF*/");
    $this->file_path = $this->content_path . $ds . 'testconfig.php';
  }

  // --------------------------------------------------------------

  function tearDown()
  {    
    $ds = DIRECTORY_SEPARATOR;

    unlink($this->content_path . $ds . 'testconfig.php');
    rmdir($this->content_path);

    parent::tearDown();
  } 

  // --------------------------------------------------------------

  public function testInstantiateAsObjectSucceeds() {

    $obj = new Configula\Drivers\Php();
    $this->assertInstanceOf('Configula\Drivers\Php', $obj);
  }

  // --------------------------------------------------------------

  public function testGetConfigReturnsCorrectItems() {
    $obj = new Configula\Drivers\Php();
    $result = $obj->read($this->file_path);

    $config = array();
    $config["a"] = "value";
    $config["b"] = array(1, 2, 3);
    $config["c"] = (object) array("d", "e", "f");

    $this->assertEquals($config, $result);
  }

}

/* EOF: PhpDriverTest.php */