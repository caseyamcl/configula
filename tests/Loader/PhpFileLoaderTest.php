<?php

namespace Configula\Loader;

/**
 * Class PhpFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PhpFileLoaderTest extends FileLoaderInterfaceTest
{

    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    protected function getExt(): string
    {
        return 'php';
    }

    /**
     * @param string $filename
     * @return FileLoaderInterface
     */
    protected function getObject(string $filename): FileLoaderInterface
    {
        return new PhpFileLoader($filename);
    }
}
