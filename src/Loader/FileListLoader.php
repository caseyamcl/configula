<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * Loader that loads from files, but ignores missing files
 *
 * @package Configula\Loader
 */
class FileListLoader implements ConfigLoaderInterface
{
    /**
     * @var iterable|\SplFileInfo[]|string[]
     */
    private $files;

    /**
     * FileListLoader constructor.
     * @param iterable|\SplFileInfo[]|string[] $files
     */
    public function __construct(iterable $files)
    {
        $this->files = $files;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     * @throws ConfigLoaderException  If loading fails for whatever reason, throw this exception
     */
    public function load(): ConfigValues
    {
        $values = new ConfigValues([]);

        foreach ($this->files as $file) {
            try {
                $newValues = (new FileLoader((string) $file))->load();
                $values = $values->merge($newValues);
            }
            catch (ConfigLoaderException $e) {
                // pass..
            }
        }

        return $values;
    }
}