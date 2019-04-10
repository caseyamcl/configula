<?php

namespace Configula;

use CallbackFilterIterator;
use Configula\Exception\ConfigFileNotFoundException;
use Configula\Exception\ConfigLogicException;
use Configula\Loader\ArrayValuesLoader;
use Configula\Loader\CascadingConfigLoader;
use Configula\Loader\FileListLoader;
use Configula\Loader\FolderLoader;
use Configula\Loader\ConfigLoaderInterface;
use Configula\Loader\DecidingFileLoader;
use DirectoryIterator;
use SplFileInfo;

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
     * @param  array $items
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
     * @param  ConfigLoaderInterface $loader
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
     * @param  iterable|array[]|string[]|SplFileInfo[]|ConfigLoaderInterface $items
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
                case is_string($item) or $item instanceof SplFileInfo:
                    $loaders[] = new DecidingFileLoader($item);
                    break;
                default:
                    throw new ConfigLogicException(sprintf('Invalid config source: ' . gettype($item)));
            }
        }

        return (new CascadingConfigLoader($loaders ?? []))->load();
    }

    /**
     * Load from an iterator of files
     *
     * Values are loaded in cascading fashion, with files later in the iterator taking precedence
     *
     * Missing or unreadable files are ignored.
     *
     * @param  iterable|string[]|SplFileInfo[] $files
     * @return ConfigValues
     */
    public static function loadFiles(iterable $files): ConfigValues
    {
        return (new FileListLoader($files))->load();
    }

    /**
     * Build configuration by reading a single directory of files (non-recursive; ignores sub-directories)
     *
     * @param  string $configDirectory
     * @param  array  $defaults
     * @return ConfigValues
     */
    public static function loadSingleDirectory(string $configDirectory, array $defaults = []): ConfigValues
    {
        // Build an iterator that reads only files in the top-level directory
        $iterator = new CallbackFilterIterator(
            new DirectoryIterator($configDirectory),
            function (SplFileInfo $info, $key, DirectoryIterator $iterator) {
                return $info->isFile() && ! $iterator->isDot();
            }
        );

        return (new ConfigValues($defaults))->merge(static::loadFiles($iterator));
    }

    /**
     * Build configuration by recursively reading a directory of files
     *
     * @param  string $configPath Directory or file path
     * @param  array  $defaults
     * @return ConfigValues
     */
    public static function loadPath(string $configPath = '', array $defaults = []): ConfigValues
    {
        // If path, use default behavior..
        if (is_dir($configPath)) {
            $pathValues = (new FolderLoader($configPath))->load();
        } elseif (is_file($configPath)) { // Elseif if file, then just load that single file..
            $pathValues = (new DecidingFileLoader($configPath))->load();
        } elseif ($configPath === '') { // Else, no path provided, so empty values
            $pathValues = new ConfigValues([]);
        } else {
            throw new ConfigFileNotFoundException('Cannot resolve config path: ' . $configPath);
        }

        // Merge defaults and return
        return (new ConfigValues($defaults))->merge($pathValues);
    }
}
