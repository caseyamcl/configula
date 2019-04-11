<?php
/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 3.0
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

class FileListLoaderTest extends TestCase
{
    public function testLoadCascadesCorrectly(): void
    {
        $files = [
            __DIR__ . '/../fixtures/ini/config.ini',
            __DIR__ . '/../fixtures/php/config.php',
            __DIR__ . '/../fixtures/yml/config.yml'
        ];

        $values = (new FileListLoader($files))->load();
        $this->assertEquals('value', $values->get('a')); // this is the same across all config files
        $this->assertEquals('d', $values->get('c_one')); // this was from ini and doesn't get clobbered
        $this->assertEquals('e', $values->get('c.d'));   // the value of 'c' gets clobbered in the yml
    }

    public function testLoadIgnoresMissingAndUnmappedFiles(): void
    {
        $files = [
            __DIR__ . '/../fixtures/ini/config.ini',
            __DIR__ . '/../fixtures/yml/config.yml', // Not mapped and should be ignored
            __DIR__ . '/../fixtures/php/config.php',
            __DIR__ . '/../fixtures/nope/config.nope' // This file doesn't exist and should be ignored
        ];

        $extensionMap = FileLoader::DEFAULT_EXTENSION_MAP;
        unset($extensionMap['yml']);

        $values = (new FileListLoader($files, $extensionMap))->load();

        $this->assertEquals('value', $values->get('a')); // remains unchanged
        $this->assertIsObject($values->get('c')); // since YAML wasn't loaded, the value of 'c' is from config.php
    }
}
