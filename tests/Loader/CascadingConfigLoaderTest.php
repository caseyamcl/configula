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
