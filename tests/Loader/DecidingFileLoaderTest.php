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

use Configula\Exception\ConfigFileNotFoundException;
use Configula\Exception\ConfigLoaderException;
use Configula\Exception\UnmappedFileExtensionException;
use Configula\fixtures\ErrorTriggeringFileLoader;
use Error;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class DecidingFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class DecidingFileLoaderTest extends TestCase
{
    #[dataProvider('goodFileProvider')]
    public function testGoodFilesReturnExpectedResults(string $filePath): void
    {
        $values = (new FileLoader($filePath))->load();
        $this->assertEquals('value', $values->get('a'));
        $this->assertEquals([1, 2, 3], $values->get('b'));
    }

    public function testNonExistentFileThrowsException(): void
    {
        $this->expectException(ConfigFileNotFoundException::class);
        (new FileLoader(__DIR__ . '/non-existent.yml'))->load();
    }

    public function testNonMappedProviderThrowsException(): void
    {
        $this->expectException(UnmappedFileExtensionException::class);
        (new FileLoader(__DIR__ . '/../fixtures/ini/config.ini', []))->load();
    }

    public function testInvalidLoaderClassThrowsException(): void
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('must be instance of');
        (new FileLoader(__DIR__ . '/../fixtures/ini/config.ini', ['ini' => stdClass::class]))->load();
    }

    public function testLoadWithExceptionPassesError(): void
    {
        $this->expectException(Error::class);
        (new FileLoader(
            __DIR__ . '/../fixtures/ini/bad.ini',
            ['ini' => ErrorTriggeringFileLoader::class]
        ))->load();
    }

    /**
     * @return array
     */
    public static function goodFileProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/ini/config.ini'],
            [__DIR__ . '/../fixtures/json/config.json'],
            [__DIR__ . '/../fixtures/yml/config.yml'],
            [__DIR__ . '/../fixtures/yml/config.yaml'],
            [__DIR__ . '/../fixtures/php/config.php']
        ];
    }
}
