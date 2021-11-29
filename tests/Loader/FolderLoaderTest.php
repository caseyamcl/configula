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
use PHPUnit\Framework\TestCase;

/**
 * Class FolderLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class FolderLoaderTest extends TestCase
{
    public function testFolderLoaderThrowsExceptionForNonexistentDirectory(): void
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('is it a directory');
        (new FolderLoader(__DIR__ . '/nonexistent'))->load();
    }

    public function testValuesLoadedInCorrectOrder(): void
    {
        $values = (new FolderLoader(__DIR__ . '/../fixtures/folder'))->load();
        $this->assertSame('a_from_config_ini', $values->get('a'));
        $this->assertSame('b-from-dist-json', $values->get('b'));
        $this->assertSame('c_from_config_local_yml', $values->get('c'));
    }
}
