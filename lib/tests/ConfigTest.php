<?php

require_once(__DIR__ . '/../Configula/Config.php');
require_once(__DIR__ . '/../Configula/DriverInterface.php');
require_once(__DIR__ . '/../Configula/Drivers/Php.php');

class ConfigTest extends PHPUnit_Framework_TestCase {

    private $content_path;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();

        $ds = DIRECTORY_SEPARATOR;
        $this->content_path = sys_get_temp_dir() . $ds . 'phpunit_configula_test_' . time();

        //Setup fake content directory
        mkdir($this->content_path);

        $php_good_code = '<?php
            $config = array();
            $config["a"] = "value";
            $config["b"] = array(1, 2, 3);
            $config["c"] = (object) array("d", "e", "f");
            $config["d"] = array("vala" => "hi", "valb" => "bye");
            $config["d"]["valc"] = array("a" => 1, "b" => 2, "c" => 3);
            /*EOF*/';

        $php_bad_code = '<?php
            $nuthin = "yep";
        ';

        file_put_contents($this->content_path . $ds . 'phpgood.php', $php_good_code);
        file_put_contents($this->content_path . $ds . 'phpbad.php',  $php_bad_code);
    }

    // --------------------------------------------------------------

    function tearDown()
    {    
        $ds = DIRECTORY_SEPARATOR;

        unlink($this->content_path . $ds . 'phpgood.php');
        unlink($this->content_path . $ds . 'phpbad.php');
        rmdir($this->content_path);

        parent::tearDown();
    } 

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds() {

        $obj = new Configula\Config();
        $this->assertInstanceOf('Configula\Config', $obj);
    }

    // --------------------------------------------------------------

    public function testObjectUsesDefaultValuesWhenNoConfigDirSpecified() {

        $defaults = array(
            'a' => 'value',
            'b' => array(1, 2, 3),
            'c' => (object) array('d' => 'e', 'f' => 'g'),
            'd' => array("vala" => "hi", "valb" => "bye", "valc" =>  array("a" => 1, "b" => 2, "c" => 3))
        );

        $obj = new Configula\Config(NULL, $defaults);

        $this->assertEquals('value', $obj->a);
        $this->assertEquals(1, $obj->b[0]);
        $this->assertEquals('e', $obj->c->d);
    }

    // --------------------------------------------------------------

    public function testObjectNonMagicInterfaceMethodWorks() {

        $defaults = array(
            'a' => 'value',
            'b' => array(1, 2, 3),
            'c' => (object) array('d' => 'e', 'f' => 'g'),
            'd' => array("vala" => "hi", "valb" => "bye")
        );

        $obj = new Configula\Config(NULL, $defaults);

        $this->assertEquals('value', $obj->getItem('a'));
        $this->assertEquals(array(1, 2, 3), $obj->getItem('b'));
        $this->assertEquals('e', $obj->getItem('c')->d);
        $this->assertEquals('hi', $obj->d['vala']);
    }
 
    // --------------------------------------------------------------

    public function testDotSyntaxRetrievesItemsCorrectly() {
 
        $obj = new Configula\Config($this->content_path);   

        $this->assertEquals(1, $obj->getItem('b.0'));
        $this->assertEquals('hi', $obj->getItem('d.vala'));
        $this->assertEquals(NULL, $obj->getItem('does.not.exist'));
    }

    // --------------------------------------------------------------

    public function testNonExistentValuesReturnsNull() {

        $defaults = array(
            'a' => 'value',
            'b' => array(1, 2, 3),
            'c' => (object) array('d' => 'e', 'f' => 'g')
        );

        $obj = new Configula\Config(NULL, $defaults);

        $this->assertEquals(NULL, $obj->non_existent);
        $this->assertEquals(NULL, $obj->getItem('doesnotexist'));
    }

    // --------------------------------------------------------------

    public function testParseConfigFileWorksForValidFile() {

        $filepath = $this->content_path . DIRECTORY_SEPARATOR . 'phpgood.php';

        $obj = new Configula\Config();
        $result = $obj->parseConfigFile($filepath);
        

        $this->assertEquals('value', $result['a']);
        $this->assertEquals(1, $result['b'][0]);
    }

    // --------------------------------------------------------------

    public function testParseConfigFileReturnsEmptyArrayForInvalidFile() {

        $filepath = $this->content_path . DIRECTORY_SEPARATOR . 'phpbad.php';

        $obj = new Configula\Config();
        $result = $obj->parseConfigFile($filepath);
        
        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testParseConfigFileThrowsExceptionForUnreadableFile() {

        $filepath = $this->content_path . 'abc' . rand('1000', '9999') . '.php';

        try {
            $obj = new Configula\Config();
            $result = $obj->parseConfigFile($filepath);
        } catch (Exception $e) {
            return;
        }

        $this->fail("Parse Config File should have thrown an exception for non-existent file: " . $filepath);

    }

    // --------------------------------------------------------------

    public function testInstantiateWithValidPathBuildsCorrectValues() {

        $obj = new Configula\Config($this->content_path);

        $this->assertEquals('value', $obj->a);
        $this->assertEquals(1, $obj->b[0]);
    }

    // --------------------------------------------------------------

    public function testInstantiateWithInvalidPathBuildsNoValues() {

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpunit_test_nothing_' . time();
        mkdir($path);
        $obj = new Configula\Config($path);

        $this->assertEquals(NULL, $obj->a);
        $this->assertEquals(array(), $obj->getItems());

        rmdir($path);
    }

    // --------------------------------------------------------------

    public function testGetItemsReturnsAnArray() {

        $obj = new Configula\Config($this->content_path);

        //Single item
        $result = $obj->getItems('a');
        $this->assertEquals('value', $result['a']);

        //Multiple items
        $result = $obj->getItems(array('a', 'b'));
        $this->assertEquals('value', $result['a']);
        $this->assertEquals(array(1, 2, 3), $result['b']);
    }

    // --------------------------------------------------------------

    public function testLocalConfigFileOverridesMainConfigFile() {
        
        $ds = DIRECTORY_SEPARATOR;
        $code = '<?php
            $config = array();
            $config["a"] = "newvalue";
            $config["c"] = (object) array("j", "k", "l");
            /*EOF*/';

        file_put_contents($this->content_path . $ds . 'phpgood.local.php', $code);

        $obj = new Configula\Config($this->content_path);

        $this->assertEquals('newvalue', $obj->a);
        $this->assertEquals((object) array('j', 'k', 'l'), $obj->c);
        $this->assertEquals(array(1, 2, 3), $obj->b);

        unlink($this->content_path . $ds . 'phpgood.local.php');
    }

 // --------------------------------------------------------------

    /**
     * Tests to ensure that the merge_config method works
     *
     * A configuration item that is itself an array might have subvalues
     * inside of it.  If the local configuration file overrides only one
     * of those subvalues, the remaining values should stay the same.
     */
    public function testLocalConfigOverwritesSubArrayItemCorrectly() {
        
        $ds = DIRECTORY_SEPARATOR;
        $code = '<?php
            $config = array();
            $config["d"]["vala"] = "newvalue";
            $config["d"]["valc"]["b"] = "newvalue";
            /*EOF*/';

        file_put_contents($this->content_path . $ds . 'phpgood.local.php', $code);

        $obj = new Configula\Config($this->content_path);

        $this->assertEquals('newvalue', $obj->d['vala']);
        $this->assertEquals('bye', $obj->d['valb']);

        $this->assertEquals(1, $obj->d['valc']['a']);
        $this->assertEquals('newvalue', $obj->d['valc']['b']);

        unlink($this->content_path . $ds . 'phpgood.local.php');
    }
}

/* EOF: PhpDriverTest.php */
