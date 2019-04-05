<?php

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
        $keys = array_keys(iterator_to_array(ArrayUtils::flattenAndIterate(static::$arrayA)));
        $this->assertEquals($expectedKeys, $keys);

        $expectedKeys = ['b', 'c.p', 'c.w'];
        $keys = array_keys(iterator_to_array(ArrayUtils::flattenAndIterate(static::$arrayB)));
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

        $this->assertEquals($expected, ArrayUtils::merge(static::$arrayA, static::$arrayB));
    }
}
