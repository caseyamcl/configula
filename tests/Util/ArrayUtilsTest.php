<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 5
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Configula\Util;

use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    private static $arrayA = [
        'a' => 'apple',
        'b' => 'banana',
        'c' => [
            'p' => 'pear',
            'w' => 'watermelon'
        ]
    ];

    private static $arrayB = [
        'b' => 'beans',
        'c' => [
            'p' => 'pineapple',
            'w' => 'watermelon'
        ]
    ];

    public function testFlattenAndIterate(): void
    {
        $expectedKeys = ['a', 'b', 'c.p', 'c.w'];
        $keys = array_keys(iterator_to_array(ArrayUtils::flattenAndIterate(self::$arrayA)));
        $this->assertEquals($expectedKeys, $keys);

        $expectedKeys = ['b', 'c.p', 'c.w'];
        $keys = array_keys(iterator_to_array(ArrayUtils::flattenAndIterate(self::$arrayB)));
        $this->assertEquals($expectedKeys, $keys);
    }

    /**
     * Test merge
     */
    public function testMerge(): void
    {
        $expected = [
            'a' => 'apple',
            'b' => 'beans',
            'c' => [
                'p' => 'pineapple',
                'w' => 'watermelon'
            ]
        ];

        $this->assertEquals($expected, ArrayUtils::merge(self::$arrayA, self::$arrayB));
    }
}
