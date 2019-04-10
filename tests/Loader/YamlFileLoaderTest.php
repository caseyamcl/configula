<?php

namespace Configula\Loader;

/**
 * Class YamlFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class YamlFileLoaderTest extends AbstractFileLoaderTest
{

    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    protected function getExt(): string
    {
        return 'yml';
    }

    /**
     * @param  string $filename
     * @return FileLoaderInterface
     */
    protected function getObject(string $filename): FileLoaderInterface
    {
        return new YamlFileLoader($filename);
    }
}
