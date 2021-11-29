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

declare(strict_types=1);

namespace Configula\Filter;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\TestCase;

/**
 * Class ExtractTopLevelItemFilterTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ExtractTopLevelItemFilterTest extends TestCase
{
    public function testTopLevelExtractedWhenExists(): void
    {
        $test = [
            'pre' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C'
            ],
            'foo' => 'bar'
        ];

        $values = (new ExtractTopLevelItemsFilter('pre'))->__invoke(new ConfigValues($test));
        $this->assertEquals('A', $values->get('a'));
        $this->assertEquals('B', $values->get('b'));
    }

    public function testNoChangeWhenTopLevelNotExists(): void
    {
        $test = [
            'baz' => 'biz',
            'foo' => 'bar'
        ];

        $values = (new ExtractTopLevelItemsFilter('pre'))->__invoke(new ConfigValues($test));
        $this->assertEquals($test, $values->getArrayCopy());
    }

    public function testExceptionThrownWhenCollisionDetected(): void
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('collision');

        $test = [
            'pre' => [
                'a' => 'A',  // conflicts with top-level item
                'b' => 'B',
                'c' => 'C'
            ],
            'a'  => 'Apple',
            'foo' => 'bar'
        ];

        (new ExtractTopLevelItemsFilter('pre'))->__invoke(new ConfigValues($test));
    }
}
