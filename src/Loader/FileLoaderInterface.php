<?php


namespace Configula\Loader;

/**
 * Interface FileLoaderInterface
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
interface FileLoaderInterface extends ConfigLoaderInterface
{
    /**
     * FileLoaderInterface constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath);
}
