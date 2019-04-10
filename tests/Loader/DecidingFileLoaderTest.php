<?php

namespace Configula\Loader;

use Configula\Exception\ConfigFileNotFoundException;
use Configula\Exception\ConfigLoaderException;
use Configula\Exception\UnmappedFileExtensionException;
use Configula\fixtures\ErrorTriggeringFileLoader;
use Error;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class DecidingFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class DecidingFileLoaderTest extends TestCase
{
    /**
     * @param        string $filePath
     * @dataProvider goodFileProvider
     */
    public function testGoodFilesReturnExpectedResults(string $filePath): void
    {
        $values = (new DecidingFileLoader($filePath))->load();
        $this->assertEquals('value', $values->get('a'));
        $this->assertEquals([1, 2, 3], $values->get('b'));
    }

    public function testNonExistentFileThrowsException()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        (new DecidingFileLoader(__DIR__ . '/non-existent.yml'))->load();
    }

    public function testNonMappedProviderThrowsException()
    {
        $this->expectException(UnmappedFileExtensionException::class);
        (new DecidingFileLoader(__DIR__ . '/../fixtures/ini/config.ini', []))->load();
    }

    public function testInvalidLoaderClassThrowsException()
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('must be instance of');
        (new DecidingFileLoader(__DIR__ . '/../fixtures/ini/config.ini', ['ini' => stdClass::class]))->load();
    }

    public function testLoadWithExceptionPassesError()
    {
        $this->expectException(Error::class);
        (new DecidingFileLoader(
            __DIR__ . '/../fixtures/ini/bad.ini',
            ['ini' => ErrorTriggeringFileLoader::class]
        ))->load();
    }

    /**
     * @return array
     */
    public function goodFileProvider(): array
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
