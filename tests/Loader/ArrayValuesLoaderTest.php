<?php

namespace Configula\Loader;

use PHPUnit\Framework\TestCase;

class ArrayValuesLoaderTest extends TestCase
{
    public static $values = ['a' => 'Apple', 'b' => 'Banana', 'c' => ['p' => 'Pineapple', 'w' => 'Watermelon']];

    public function testLoad(): void
    {
        $config = (new ArrayValuesLoader(static::$values))->load();
        $this->assertEquals('Pineapple', $config->get('c.p'));
        $this->assertEquals(4, count($config));
    }
}
