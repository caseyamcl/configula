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
use ReflectionMethod;

/**
 * Class EnvLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EnvLoaderTest extends TestCase
{
    public const DEFAULT_ENV_VARS = [
        'SOME_FOOBAR_APP_THING' => 1,
        'FOOBAR_APP_ANOTHER'    => 2
    ];

    public function testLoadReturnsEverythingInEnvironmentWithDefaultParameters(): void
    {
        $rMethod = new ReflectionMethod(EnvLoader::class, 'prepareVal');
        $rMethod->setAccessible(true);

        $expected = array_map(
            function ($val) use ($rMethod) {
                return $rMethod->invoke(new EnvLoader(), $val);
            },
            getenv()
        );

        $this->assertEquals($expected, (new EnvLoader())->load()->getArrayCopy());
    }

    /**
     *
     */
    public function testLoadSetsAllValuesToLowerCaseIfSpecified()
    {
        $this->assertEquals(
            array_map('strtolower', array_keys(getenv())),
            array_keys((new EnvLoader('', '', true))->load()->getArrayCopy())
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadLimitsResultsBasedOnRegexString()
    {
        $this->setupEnv();
        $this->assertEquals(2, (new EnvLoader('/FOOBAR_APP/'))->load()->count());
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadLowerCasesValuesCorrectly()
    {
        $this->setupEnv();
        $values = (new EnvLoader('/FOOBAR_APP/', '', true))->load();
        $this->assertEquals(
            array_map('strtolower', array_keys(static::DEFAULT_ENV_VARS)),
            array_keys($values->getArrayCopy())
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadDelimitsCorrectly()
    {
        $this->setupEnv();
        $values = (new EnvLoader('/FOOBAR_APP/', '_'))->load();
        $this->assertEquals(
            [
                'SOME' => ['FOOBAR' => ['APP' => ['THING' => 1]]],
                'FOOBAR' => ['APP' => ['ANOTHER' => 2]]
            ],
            $values->getArrayCopy()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadUsingPrefixWorksCorrectly()
    {
        $this->setupEnv();
        $values = EnvLoader::loadUsingPrefix('FOOBAR_');
        $this->assertEquals(['APP_ANOTHER' => 2], $values->getArrayCopy());
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadUsingPrefixWorksWhenLowerCaseEnabled()
    {
        $this->setupEnv();
        $values = EnvLoader::loadUsingPrefix('FOOBAR_', '', true);
        $this->assertEquals(['app_another' => 2], $values->getArrayCopy());
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadUsingPrefixWorksWithDelimiter()
    {
        $this->setupEnv();
        $values = EnvLoader::loadUsingPrefix('FOOBAR', '_', true);
        $this->assertEquals(['app' => ['another' => 2]], $values->getArrayCopy());
    }

    public function testLoadUsingPrefixWorksWithMultiLevelDelimiter()
    {
        $this->setupEnv();
        $values = EnvLoader::loadUsingPrefix('FOOBAR_APP', '_');
        $this->assertEquals(2, $values->get('ANOTHER'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testPrepareValuesSetsScalarsCorrectly()
    {
        putenv('FOOBAR_INTEGER=2');
        putenv('FOOBAR_FLOAT=2.3');
        putenv('FOOBAR_TRUE=true');
        putenv('FOOBAR_FALSE=false');
        putenv('FOOBAR_NULL=null');
        putenv('FOOBAR_STR=somestuff');

        $values = EnvLoader::loadUsingPrefix('FOOBAR_');
        $this->assertSame(2, $values->get('INTEGER'));
        $this->assertSame(2.3, $values->get('FLOAT'));
        $this->assertSame(true, $values->get('TRUE'));
        $this->assertSame(false, $values->get('FALSE'));
        $this->assertSame(null, $values->get('NULL'));
        $this->assertSame('somestuff', $values->get('STR'));
    }

    protected function setupEnv(): void
    {
        // Pre-test
        if ((new EnvLoader('/FOOBAR_APP/'))->load()->count() !== 0) {
            $this->markTestSkipped('Skipping (there are environment variables with "FOOBAR_APP" in them already?!)');
        }

        foreach (static::DEFAULT_ENV_VARS as $name => $val) {
            putenv(sprintf("%s=%s", $name, $val));
        }
    }
}
