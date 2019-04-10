<?php


namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\TestCase;

abstract class AbstractFileLoaderTest extends TestCase
{
    const LOADER_DIRECTORY = __DIR__ . '/../fixtures';

    public function testGoodFileReturnsExpectedItems()
    {
        $goodFilePath = $this->getTestFilePath('config');
        if (! is_readable($goodFilePath)) {
            $this->fail('Missing expected "good" test case for file type: ' . $this->getExt());
        }

        $loader = $this->getObject($goodFilePath);
        $values = $loader->load();

        $this->assertEquals('value', $values->get('a'));
        $this->assertEquals([1, 2, 3], $values->get('b'));
    }

    public function testBadFileThrowsLoaderException()
    {
        $badFilePath = $this->getTestFilePath('bad');
        if (! is_readable($badFilePath)) {
            $this->markTestSkipped('Missing expected "bad" test case for file type: ' . $this->getExt());
        }

        $this->expectException(ConfigLoaderException::class);
        $loader = $this->getObject($badFilePath);
        $loader->load();
    }

    public function testEmptyFileReturnsEmptyConfig()
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
