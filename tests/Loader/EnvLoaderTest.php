<?php

namespace Configula\Loader;

use PHPUnit\Framework\TestCase;

/**
 * Class EnvLoaderTest
 * @package Configula\Loader
 */
class EnvLoaderTest extends TestCase
{
    public function testInstantiateAsObjectSucceeds()
    {
        $obj = new EnvLoader();
        $this->assertInstanceOf(EnvLoader::class, $obj);
    }

    public function testDefaultSettingsBehaveAsExpected()
    {
        $_ENV['FOO']   = 'bar';
        $_ENV['BAZ_A'] = 'A';
        $_ENV['BAZ_B'] = 'B';
        $_ENV['BAZ_C'] = 'C';

        $loader = new EnvLoader();
        $config = $loader->load();

        $this->assertEquals('bar', $config('foo'));
        $this->assertEquals('A',   $config('baz.a'));
        $this->assertEquals('B',   $config('baz.b'));
        $this->assertEquals('C',   $config('baz.c'));
    }

    public function testPrepareValueInterpretsValuesCorrectly()
    {
        $_ENV['IS_TRUE']   = 'true';
        $_ENV['IS_FALSE']  = 'false';
        $_ENV['IS_NIL']    = 'null';
        $_ENV['IS_STRING'] = 'blah';

        $loader = new EnvLoader();
        $config = $loader->load();

        $this->assertTrue($config('is.true'));
        $this->assertFalse($config('is.false'));
        $this->assertNull($config('is.nil'));
        $this->assertInternalType('string', $config('is.string'));
    }

    public function testSettingPrefixStripsPrefixFromEnvName()
    {
        $_ENV['MYAPP_VALUE_1'] = 1;
        $_ENV['MYAPP_VALUE_2'] = 2;
        $_ENV['MYAPP_VALUE_3'] = 3;

        $loader = new EnvLoader('MYAPP_');
        $config = $loader->load();

        $this->assertEquals(1, $config('value.1'));
        $this->assertEquals(2, $config('value.2'));
        $this->assertEquals(3, $config('value.3'));
    }

    public function testSettingDelimiterManuallyWorksCorrectly()
    {
        $_ENV['VALUE-1'] = 1;
        $_ENV['VALUE-2'] = 2;
        $_ENV['VALUE-3'] = 3;

        $loader = new EnvLoader('', '-');
        $config = $loader->load();

        $this->assertEquals(1, $config('value.1'));
        $this->assertEquals(2, $config('value.2'));
        $this->assertEquals(3, $config('value.3'));
    }

    public function testDisableStrTolowerRetainsCase()
    {
        $_ENV['VALUE_1'] = 1;
        $_ENV['VALUE_2'] = 2;
        $_ENV['VALUE_3'] = 3;

        $loader = new EnvLoader('', '_', false);
        $config = $loader->load();

        $this->assertEquals(1, $config('VALUE.1'));
        $this->assertEquals(2, $config('VALUE.2'));
        $this->assertEquals(3, $config('VALUE.3'));
    }

    public function testSettingNoDelimiterRetainsFlatStructure()
    {
        $_ENV['VALUE_ONE']   = 1;
        $_ENV['VALUE_TWO']   = 2;
        $_ENV['VALUE_THREE'] = 3;

        $loader = new EnvLoader('', null);
        $config = $loader->load();

        $this->assertEquals(1, $config('value_one'));
        $this->assertEquals(2, $config('value_two'));
        $this->assertEquals(3, $config('value_three'));

    }

    public function testNoDelimiterAndNoStrToLowerMaintainsVariableName()
    {
        $_ENV['VALUE_ONE']   = 1;
        $_ENV['VALUE_TWO']   = 2;
        $_ENV['VALUE_THREE'] = 3;

        $loader = new EnvLoader('', null, false);
        $config = $loader->load();

        $this->assertEquals(1, $config('VALUE_ONE'));
        $this->assertEquals(2, $config('VALUE_TWO'));
        $this->assertEquals(3, $config('VALUE_THREE'));
    }
}
