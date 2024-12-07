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

abstract class AbstractFileLoaderTester extends TestCase
{
    protected const LOADER_DIRECTORY = __DIR__ . '/../fixtures';
    protected const GOOD_FILE_NAME = 'config';

    public function testGoodFileReturnsExpectedItems(): void
    {
        $goodFilePath = $this->getTestFilePath(static::GOOD_FILE_NAME);
        if (! is_readable($goodFilePath)) {
            $this->fail('Missing expected "good" test case for file type: ' . $this->getExt());
        }

        $loader = $this->getObject($goodFilePath);
        $values = $loader->load();

        $this->assertEquals('value', $values->get('a'));
        $this->assertEquals([1, 2, 3], $values->get('b'));
    }

    public function testBadFileThrowsLoaderException(): void
    {
        $badFilePath = $this->getTestFilePath('bad');
        if (! is_readable($badFilePath)) {
            $this->markTestSkipped('Missing expected "bad" test case for file type: ' . $this->getExt());
        }

        $this->expectException(ConfigLoaderException::class);
        $loader = $this->getObject($badFilePath);
        $loader->load();
    }

    public function testEmptyFileReturnsEmptyConfig(): void
    {
        $emptyFilePath = $this->getTestFilePath('empty');
        if (! is_readable($emptyFilePath)) {
            $this->markTestSkipped('Missing expected "empty" test case for file type: ' . $this->getExt());
        }

        $loader = $this->getObject($emptyFilePath);
        $this->assertSame(0, $loader->load()->count());
    }

    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    abstract protected function getExt(): string;

    /**
     * @param  string $filename
     * @return FileLoaderInterface
     */
    abstract protected function getObject(string $filename): FileLoaderInterface;

    /**
     * @param  string $baseFileName
     * @return string
     */
    protected function getTestFilePath(string $baseFileName): string
    {
        return sprintf(
            "%s/%s/%s.%s",
            static::LOADER_DIRECTORY,
            $this->getExt(),
            $baseFileName,
            $this->getExt()
        );
    }
}
