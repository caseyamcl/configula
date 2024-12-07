<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 4
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
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

    #[RunInSeparateProcess]
    public function testValidDataReturnsExpectedResults(): void
    {
        putenv('FOOBAR_JSON_DATA=' . json_encode(static::JSON_DATA));
        $values = (new JsonEnvLoader('FOOBAR_JSON_DATA'))->load();
        $this->assertIsObject($values->get('d'));
        $this->assertIsFloat($values->get('c'));
        $this->assertIsInt($values->get('b'));
        $this->assertIsString($values->get('a'));
    }

    public function testInvalidDataThrowsLoaderException(): void
    {
        putenv('FOOBAR_JSON_DATA=asdf1239423-497y8-398289--83--@#_#@*_#*_');

        $this->expectException(ConfigLoaderException::class);
        (new JsonEnvLoader('FOOBAR_JSON_DATA'))->load();
    }

    #[RunInSeparateProcess]
    public function testValidDataReturnsExpectedResultsWhenArrayOptionEnabled(): void
    {
        putenv('FOOBAR_JSON_DATA=' . json_encode(static::JSON_DATA));
        $values = (new JsonEnvLoader('FOOBAR_JSON_DATA', true))->load();
        $this->assertSame('value', $values->get('d.some'));
        $this->assertSame('value', $values->get('d.another'));
    }
}
