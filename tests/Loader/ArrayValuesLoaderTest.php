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

use PHPUnit\Framework\TestCase;

class ArrayValuesLoaderTest extends TestCase
{
    public static $values = ['a' => 'Apple', 'b' => 'Banana', 'c' => ['p' => 'Pineapple', 'w' => 'Watermelon']];

    public function testLoad(): void
    {
        $config = (new ArrayValuesLoader(static::$values))->load();
        $this->assertEquals('Pineapple', $config->get('c.p'));
        $this->assertCount(4, $config);
    }
}
