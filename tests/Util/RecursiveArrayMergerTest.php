<?php

namespace Configula\Util;

use PHPUnit\Framework\TestCase;

class RecursiveArrayMergerTest extends TestCase
{
    /**
     * Test merge
     */
    public function testMerge(): void
    {
        $one = [
            'a' => 'apple',
            'b' => 'banana',
            'c' => [
                'p' => 'pear',
                'w' => 'watermelon'
            ]
        ];

        $two = [
            'b' => 'beans',
            'c' => [
                'p' => 'pineapple',
                'w' => 'watermelon'
            ]
        ];

        $expected = [
            'a' => 'apple',
            'b' => 'beans',
            'c' => [
                'p' => 'pineapple',
                'w' => 'watermelon'
            ]
        ];

        $this->assertEquals($expected, RecursiveArrayMerger::merge($one, $two));
    }
}
