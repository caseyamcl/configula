<?php

namespace Configula;

use CallbackFilterIterator;
use Configula\Exception\ConfigLogicException;
use Configula\Loader\ArrayValuesLoader;
use Configula\Loader\CascadingConfigLoader;
use Configula\Loader\ConfigFolderFilesLoader;
use Configula\Loader\ConfigLoaderInterface;
use Configula\Loader\FileLoader;

/**
 * Config Facade Class
 *
 * Provides convenience methods to load configuration using many different strategies in one-step
 *
 * @package Configula
 */
class ConfigFactory
{
    /**
     * Build configuration from array
     *
     * @param array $items
     * @return ConfigValues
     */
    public static function fromArray(array $items): ConfigValues
    {
        return new ConfigValues($items);
    }

    /**
     * Load config using single loader
     *
     * This is the same as simply calling `$loader->load()`
     *
     * @param ConfigLoaderInterface $loader
     * @return ConfigValues
     */
    public static function load(ConfigLoaderInterface $loader): ConfigValues
    {
        return $loader->load();
    }

    /**
     * Load from multiple sources
     *
     * Pass in an iterable list of multiple loaders, file names, or arrays of values
     *
     * @param iterable|array[]|string[]|\SplFileInfo[]|ConfigLoaderInterface $items
     * @return ConfigValues
     */
    public static function loadMultiple(iterable $items): ConfigValues
    {
        foreach ($items as $item) {
            switch (true) {
                case $item instanceof ConfigLoaderInterface:
                    $loaders[] = $item;
                    break;
                case is_array($item):
                    $loaders[] = new ArrayValuesLoader($item);
                    break;
                case is_string($item) OR $item instanceof \SplFileInfo:
                    $loaders[] = new FileLoader($item);
                    break;
                default:
                    throw new ConfigLogicException(sprintf(
                        'Invalid config source: %s',
                        gettype($item)
                    ));
            }
        }

        return (new CascadingConfigLoader($loaders ?? []))->load();
    }

    /**
     * Load from an iterator of files
     *
     * Values are loaded in cascading fashion, with files later in the iterator taking precedence
     *
     * @param iterable|string[]|\SplFileInfo[] $files
     * @return ConfigValues
     */
    public static function loadFiles(iterable $files): ConfigValues
    {
        foreach ($files as $file) {
            $loaders[] = new FileLoader((string) $file);
        }

        return (new CascadingConfigLoader($loaders ?? []))->load();
    }

    /**
     * Build configuration by reading a single directory of files (ignores sub-directories)
     *
     * @param string $configDirectory
     * @param array $defaults
     * @return ConfigValues
     */
    public static function loadSingleDirectory(string $configDirectory, array $defaults = []): ConfigValues
    {
        // Build an iterator that reads only files in the top-level directory
        $iterator = new CallbackFilterIterator(new \DirectoryIterator($configDirectory), function(\SplFileInfo $info, $key, \DirectoryIterator $iterator) {
            return $info->isFile() && ! $iterator->isDot();
        });

        return (new ConfigValues($defaults))->merge(static::loadFiles($iterator));
    }

    /**
     * Build configuration by recursively reading a directory of files
     *
     * @param string|null $configPath Directory or file path
     * @param array $defaults
     * @return ConfigValues
     */
    public static function loadPath(string $configPath = null, array $defaults = []): ConfigValues
    {
        // If path, use default behavior..
        if (is_dir($configPath)) {
            $pathValues = (new ConfigFolderFilesLoader($configPath))->load();
        }
        elseif (is_file($configPath)) { // Elseif if file, then just load that single file..
            $pathValues = (new FileLoader($configPath))->load();
        }
        else { // Else, no path provided, so empty values
            $pathValues = new ConfigValues([]);
        }

        // Merge defaults and return
        return (new ConfigValues($defaults))->merge($pathValues);
    }
}