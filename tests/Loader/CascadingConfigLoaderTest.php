<?php

namespace Configula\Loader;

use PHPUnit\Framework\TestCase;

class CascadingConfigLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loaderOne = new ArrayValuesLoader(['a' => 'a', 'b' => 'b', 'c' => ['d' => 'd', 'e' => 'e']]);
        $loaderTwo = new ArrayValuesLoader(['b' => 'B', 'c' => ['e' => 'E']]);

        $values = (new CascadingConfigLoader([$loaderOne, $loaderTwo]))->load();
        $this->assertSame('a', $values->get('a'));
        $this->assertSame('B', $values->get('b'));
        $this->assertSame('d', $values->get('c.d'));
        $this->assertSame('E', $values->get('c.e'));
    }
}
