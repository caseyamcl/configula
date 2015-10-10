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
 * Config Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ConfigTest extends PHPUnit_Framework_TestCase {

    private $configPath;

    // --------------------------------------------------------------

    function setUp()
    {
        parent::setUp();
        $this->configPath = realpath(__DIR__ . '/fixtures/main/');
        $this->configPhpGoodFilePath = $this->configPath . '/phpgood.php';
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds() 
    {
        $obj = new Configula\Config();
        $this->assertInstanceOf('Configula\Config', $obj);
    }

    // --------------------------------------------------------------

    public function testObjectUsesDefaultValuesWhenNoConfigDirSpecified()
    {
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

    public function testObjectUsesConfigFileEvenWithDefaultsSpecified()
    {
        $defaults = array(
            'x' => 'xvalue',
            'y' => array(8, 9, 10),
            'z' => (object) array('d' => 'e', 'f' => 'g')
        );

        $obj = new Configula\Config($this->configPath, $defaults);

        $this->assertEquals("value", $obj->a);
        $this->assertEquals(array(1, 2, 3), $obj->b);
        $this->assertEquals('xvalue', $obj->x);
        $this->assertEquals(array(8, 9, 10), $obj->y);
    }


    // --------------------------------------------------------------

    public function testObjectNonMagicInterfaceMethodWorks()
    {
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

    public function testDotSyntaxRetrievesItemsCorrectly()
    {
        $obj = new Configula\Config($this->configPath);   

        $this->assertEquals(1, $obj->getItem('b.0'));
        $this->assertEquals('hi', $obj->getItem('d.vala'));
        $this->assertEquals(NULL, $obj->getItem('does.not.exist'));
    }

    // --------------------------------------------------------------

    public function testNonExistentValuesReturnsNull()
    {
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

    public function testParseConfigFileWorksForValidFile()
    {
        $filepath = $this->configPath . DIRECTORY_SEPARATOR . 'phpgood.php';

        $obj = new Configula\Config();
        $result = $obj->parseConfigFile($filepath);
        

        $this->assertEquals('value', $result['a']);
        $this->assertEquals(1, $result['b'][0]);
    }

    // --------------------------------------------------------------

    public function testParseConfigFileReturnsEmptyArrayForInvalidFile()
    {
        $filepath = $this->configPath . DIRECTORY_SEPARATOR . 'phpbad.php';

        $obj = new Configula\Config();
        $result = $obj->parseConfigFile($filepath);
        
        $this->assertEquals(array(), $result);
    }

    // --------------------------------------------------------------

    public function testParseConfigFileThrowsExceptionForUnreadableFile()
    {
        $filepath = $this->configPath . 'abc' . rand('1000', '9999') . '.php';

        try {
            $obj = new Configula\Config();
            $result = $obj->parseConfigFile($filepath);
        } catch (Exception $e) {
            return;
        }

        $this->fail("Parse Config File should have thrown an exception for non-existent file: " . $filepath);

    }

    // --------------------------------------------------------------

    public function testInstantiateWithValidPathBuildsCorrectValues()
    {
        $obj = new Configula\Config($this->configPath);

        $this->assertEquals('value', $obj->a);
        $this->assertEquals(1, $obj->b[0]);
    }

    // --------------------------------------------------------------

    public function testInstantiateWithInvalidPathBuildsNoValues() 
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpunit_test_nothing_' . time();
        mkdir($path);
        $obj = new Configula\Config($path);

        $this->assertEquals(NULL, $obj->a);
        $this->assertEquals(array(), $obj->getItems());

        rmdir($path);
    }

    // --------------------------------------------------------------

    public function testGetItemsReturnsAnArray()
    {
        $obj = new Configula\Config($this->configPath);

        //Single item
        $result = $obj->getItems('a');
        $this->assertEquals('value', $result['a']);

        //Multiple items
        $result = $obj->getItems(array('a', 'b'));
        $this->assertEquals('value', $result['a']);
        $this->assertEquals(array(1, 2, 3), $result['b']);
    }

    // --------------------------------------------------------------

    public function testLocalConfigFileOverridesMainConfigFile()
    {    
        if ( ! is_writable($this->configPath)) {
            $this->markTestSkipped("Could not write temporary file to config path.");
            return;
        }

        $ds = DIRECTORY_SEPARATOR;
        $code = '<?php
            $config = array();
            $config["a"] = "newvalue";
            $config["c"] = (object) array("j", "k", "l");
            /*EOF*/';

        file_put_contents($this->configPath . $ds . 'phpgood.local.php', $code);

        $obj = new Configula\Config($this->configPath);

        $this->assertEquals('newvalue', $obj->a);
        $this->assertEquals((object) array('j', 'k', 'l'), $obj->c);
        $this->assertEquals(array(1, 2, 3), $obj->b);

        unlink($this->configPath . $ds . 'phpgood.local.php');
    }

    // --------------------------------------------------------------

    /**
     * Tests to ensure that the merge_config method works
     *
     * A configuration item that is itself an array might have subvalues
     * inside of it.  If the local configuration file overrides only one
     * of those subvalues, the remaining values should stay the same.
     */
    public function testLocalConfigOverwritesSubArrayItemCorrectly()
    {    
        if ( ! is_writable($this->configPath)) {
            $this->markTestSkipped("Could not write temporary file to config path.");
            return;
        }

        $ds = DIRECTORY_SEPARATOR;
        $code = '<?php
            $config = array();
            $config["d"]["vala"] = "newvalue";
            $config["d"]["valc"]["b"] = "newvalue";
            /*EOF*/';

        file_put_contents($this->configPath . $ds . 'phpgood.local.php', $code);

        $obj = new Configula\Config($this->configPath);

        $this->assertEquals('newvalue', $obj->d['vala']);
        $this->assertEquals('bye', $obj->d['valb']);

        $this->assertEquals(1, $obj->d['valc']['a']);
        $this->assertEquals('newvalue', $obj->d['valc']['b']);

        unlink($this->configPath . $ds . 'phpgood.local.php');
    }

    // --------------------------------------------------------------

    public function testLoadConfigFileLoadsFile()
    {
        $obj = new Configula\Config();
        $obj->loadConfgFile($this->configPhpGoodFilePath);

        $this->assertEquals('value', $obj->a);
    }

    // --------------------------------------------------------------

    /**
     * Test that single config value load properly overwrites previous values
     */
    public function testLoadConfigFileOverridesCurrectValue()
    {
        if ( ! is_writable($this->configPath)) {
            $this->markTestSkipped("Could not write temporary file to config path.");
            return;
        }

        $ds = DIRECTORY_SEPARATOR;
        $code = '<?php
            $config = array();
            $config["d"]["vala"] = "newvalue";
            $config["d"]["valc"]["b"] = "newvalue";
            /*EOF*/';

        $obj = new Configula\Config($this->configPath);
        file_put_contents($this->configPath . $ds . 'phpextra.php', $code);
        $obj->loadConfgFile($this->configPath . $ds . 'phpextra.php');

        $this->assertEquals('bye', $obj->d['valb']);
        $this->assertEquals('newvalue', $obj->d['vala']);

        unlink($this->configPath . $ds . 'phpextra.php');

    }

    // --------------------------------------------------------------

    public function testCountableInterfaceSucceeds()
    {
        $obj = new Configula\Config($this->configPath);
        $this->assertEquals(4, count($obj));
    }

    // --------------------------------------------------------------

    public function testIteratorSucceeds()
    {
        $obj = new Configula\Config($this->configPath);

        $actualArr = array();
        foreach($obj as $key => $val) {
            $actualArr[$key] = $val;
        }        

        $expectedArr = array(
            'a' => 'value',
            'b' => array(1, 2, 3),
            'c' => (object) array('d', 'e', 'f'),
            'd' => array('vala' => 'hi', 'valb' => 'bye', 'valc' => array(
                'a' => 1, 'b' => 2, 'c' => 3
            ))
        );

        $this->assertEquals($expectedArr, $actualArr);
    }

    // --------------------------------------------------------------

    public function testArrayAccessSucceeds()
    {
        $obj = new Configula\Config($this->configPath);
        
        $this->assertEquals('value', $obj['a']);        
        $this->assertEquals(array(1, 2, 3), $obj['b']);
    }

    // --------------------------------------------------------------

    public function testArrayAccessImmutableAndThrowsExceptionForNewValue()
    {
        $obj = new Configula\Config($this->configPath);
        $this->setExpectedException('\RuntimeException');

        $obj['newValue'] = 'hello';
    }

    // --------------------------------------------------------------

    public function testArrayAccessImmutableAndThrowsExceptionForUnset()
    {
        $obj = new Configula\Config($this->configPath);
        $this->setExpectedException('\RuntimeException');
        unset($obj['a']);
    }
    
    // ---------------------------------------------------------------
    
    public function testNumberedConfiguatroniArrayKeysAreNotClobbered()
    {
        $obj = new Configula\Config();
        $result = $obj->parseConfigFile($this->configPath . '/../php/numbered.php');
        
        $this->assertEquals(array(1,2,3,5), array_keys($result));
        
    }
}

/* EOF: PhpDriverTest.php */
