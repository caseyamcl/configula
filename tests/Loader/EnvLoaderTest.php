<?php

namespace Configula\Loader;

use PHPUnit\Framework\TestCase;

/**
 * Class EnvLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EnvLoaderTest extends TestCase
{
    public function testLoadReturnsEverythingInEnvironmentWithDefaultParameters(): void
    {
        $this->assertEquals(getenv(), (new EnvLoader())->load()->getArrayCopy());
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
        // Pre-test
        if ((new EnvLoader('/FOOBAR_APP/'))->load()->count() !== 0) {
            $this->markTestSkipped('Skipping (there are environment variables with "FOOBAR_APP" in them already?!)');
        }

        putenv('SOME_FOOBAR_APP_THING=1');
        putenv('FOOBAR_APP_ANOTHER=2');

        $this->assertEquals(2, (new EnvLoader('/FOOBAR_APP/'))->load()->count());
    }

    public function testLoadLowerCasesValuesCorrectly()
    {

    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadDelimitsCorrectly()
    {

    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadUsingPrefixWorksCorrectly()
    {

    }

    /**
     * @runInSeparateProcess
     */
    public function testLoadUsingPrefixWorksWhenLowerCaseEnabled()
    {

    }

    /**
     * @runInSeparateProcess
     */
    public function testPrepareValuesSetsScalarsCorrectly()
    {

    }
}
