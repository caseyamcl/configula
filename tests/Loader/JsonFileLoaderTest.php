<?php

namespace Configula\Loader;

/**
 * Class JsonFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class JsonFileLoaderTest extends AbstractFileLoaderTest
{
    public function testExpectedObjectIsObject(): void
    {
        $config = $this->getObject($this->getTestFilePath('config'))->load();
        $this->assertIsObject($config->get('c'));
    }

    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    protected function getExt(): string
    {
        return 'json';
    }

    /**
     * @param string $filename
     * @return FileLoaderInterface
     */
    protected function getObject(string $filename): FileLoaderInterface
    {
        return new JsonFileLoader($filename);
    }
}
