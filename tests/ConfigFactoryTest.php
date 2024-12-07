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

namespace Configula;

use Configula\Exception\ConfigFileNotFoundException;
use Configula\Loader\ArrayValuesLoader;
use Configula\Loader\EnvLoader;
use Configula\Loader\EnvLoaderTest;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use stdClass;

class ConfigFactoryTest extends TestCase
{
    public function testFromArray(): void
    {
        $values = ConfigFactory::fromArray(['a' => 'A', 'b' => 'B', 'c' => 'C']);
        $this->assertSame('A', $values->get('a'));
        $this->assertSame('B', $values->get('b'));
        $this->assertSame('C', $values->get('c'));
    }

    public function testLoad()
    {
        $values = ConfigFactory::load(new ArrayValuesLoader(['a' => 'A', 'b' => 'B', 'c' => 'C']));
        $this->assertSame('A', $values->get('a'));
        $this->assertSame('B', $values->get('b'));
        $this->assertSame('C', $values->get('c'));
    }

    public function testLoadMultiple()
    {
        $sources = [
            new ArrayValuesLoader(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D']),
            __DIR__ . '/fixtures/yml/config.yaml', // File
            new SplFileInfo(__DIR__ . '/fixtures/json/config.json'),
            ['a' => 'Apple']
        ];

        $values = ConfigFactory::loadMultiple($sources);

        $this->assertEquals('Apple', $values->get('a')); // from last array
        $this->assertEquals([1, 2, 3], $values->get('b')); // from YAML file
        $this->assertIsObject($values->get('c')); // from JSON file
        $this->assertEquals('D', $values->get('d'));
    }

    public function testLoadMultipleThrowsExceptionWithInvalidInput()
    {
        $this->expectException(InvalidArgumentException::class);

        $sources = [
            new ArrayValuesLoader(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D']),
            new stdClass() // invalid.
        ];

        ConfigFactory::loadMultiple($sources);
    }

    public function testLoadFiles()
    {
        $sources = [
            __DIR__ . '/fixtures/php/config.php',
            __DIR__ . '/fixtures/yml/config.yaml'
        ];

        $values = ConfigFactory::loadFiles($sources);
        $this->assertEquals('value', $values->get('a')); // from config.php
        $this->assertEquals([1, 2, 3], $values->get('b')); // from config.yaml
    }

    public function testLoadPathWorksWithFolder()
    {
        $values = ConfigFactory::loadPath(__DIR__ . '/fixtures/folder', ['foo' => 'bar']);
        $this->assertEquals('c_from_config_local_yml', $values->get('c'));
        $this->assertEquals('bar', $values->get('foo'));
        $this->assertEquals('a_from_config_ini', $values->get('a'));
    }

    public function testLoadPathWorksWithFile()
    {
        $values = ConfigFactory::loadPath(__DIR__ . '/fixtures/folder/config.ini', ['foo' => 'bar']);
        $this->assertEquals('bar', $values->get('foo'));
        $this->assertEquals('a_from_config_ini', $values->get('a'));
    }

    public function testLoadPathReturnsOnlyDefaultsWithNull()
    {
        $values = ConfigFactory::loadPath('', ['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $values->getArrayCopy());
    }

    public function testLoadPathThrowsExceptionIfPathIsUnreadable()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        ConfigFactory::loadPath(__DIR__ . '/nope-does-not-exist.yml');
    }

    public function testLoadSingleDirectory()
    {
        $values = ConfigFactory::loadSingleDirectory(__DIR__ . '/fixtures/folder', ['foo' => 'bar']);
        $this->assertEquals('bar', $values->get('foo'));
        $this->assertEquals('a_from_config_ini', $values->get('a'));
        $this->assertEquals('c_from_config_local_yml', $values->get('c')); // clobbers c_from_config_ini
        $this->assertFalse($values->has('b'));  // subfolder should not be loaded, so no 'b' value
    }

    #[RunInSeparateProcess]
    public function testLoadEnv()
    {
        // Pre-test
        if ((new EnvLoader('/FOOBAR_APP/'))->load()->count() !== 0) {
            $this->markTestSkipped('Skipping (there are environment variables with "FOOBAR_APP" in them already?!)');
        }

        foreach (EnvLoaderTest::DEFAULT_ENV_VARS as $name => $val) {
            putenv(sprintf("%s=%s", $name, $val));
        }

        $values = ConfigFactory::loadEnv('FOOBAR_APP', '_', true);
        $this->assertSame(2, $values->get('another'));
    }

    #[RunInSeparateProcess]
    public function testLoadEnvRegex()
    {
        // Pre-test
        if ((new EnvLoader('/FOOBAR_APP/'))->load()->count() !== 0) {
            $this->markTestSkipped('Skipping (there are environment variables with "FOOBAR_APP" in them already?!)');
        }

        foreach (EnvLoaderTest::DEFAULT_ENV_VARS as $name => $val) {
            putenv(sprintf("%s=%s", $name, $val));
        }

        $values = ConfigFactory::loadEnvRegex('/FOOBAR_APP/', '_', true);
        $this->assertSame(1, $values->get('some.foobar.app.thing'));
        $this->assertSame(2, $values->get('foobar.app.another'));
    }
}
