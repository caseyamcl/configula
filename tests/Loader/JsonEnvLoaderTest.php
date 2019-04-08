<?php

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\TestCase;

class JsonEnvLoaderTest extends TestCase
{
    protected const JSON_DATA = [
        'a' => 'value',
        'b' => 1234,
        'c' => 56.3,
        'd' => [
            'some'    => 'value',
            'another' => 'value'
        ]
    ];

    /**
     * @runInSeparateProcess
     */
    public function testValidDataReturnsExpectedResults()
    {
        putenv('FOOBAR_JSON_DATA=' . json_encode(static::JSON_DATA));
        $values = (new JsonEnvLoader('FOOBAR_JSON_DATA'))->load();
        $this->assertIsObject($values->get('d'));
        $this->assertIsFloat($values->get('c'));
        $this->assertIsInt($values->get('b'));
        $this->assertIsString($values->get('a'));
    }

    public function testInvalidDataThrowsLoaderException()
    {
        putenv('FOOBAR_JSON_DATA=asdf1239423-497y8-398289--83--@#_#@*_#*_');

        $this->expectException(ConfigLoaderException::class);
        (new JsonEnvLoader('FOOBAR_JSON_DATA'))->load();
    }

    /**
     * @runInSeparateProcess
     */
    public function testValidDataReturnsExpectedResultsWhenArrayOptionEnabled()
    {
        putenv('FOOBAR_JSON_DATA=' . json_encode(static::JSON_DATA));
        $values = (new JsonEnvLoader('FOOBAR_JSON_DATA', true))->load();
        $this->assertSame('value', $values->get('d.some'));
        $this->assertSame('value', $values->get('d.another'));
    }
}
