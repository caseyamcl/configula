<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * File Loader
 *
 * @package Configula\Loader
 */
class FileLoader implements ConfigLoaderInterface
{
    const DEFAULT_EXTENSION_MAP = [
        'yml'  => YamlFileLoader::class,
        'yaml' => YamlFileLoader::class,
        'json' => JsonFileLoader::class,
        'php'  => PhpFileLoader::class,
        'ini'  => IniFileLoader::class
    ];

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var array
     */
    private $extensionMap;

    /**
     * FileLoader constructor.
     *
     * @param string $filePath
     * @param array $extensionMap
     */
    public function __construct(string $filePath, array $extensionMap = self::DEFAULT_EXTENSION_MAP)
    {
        $this->extensionMap = $extensionMap;
        $this->filePath = $filePath;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        // Check valid path
        if (! is_readable($this->filePath) OR ! is_file($this->filePath)) {
            throw new ConfigLoaderException(
                'Cannot read from file path (does it exist? is it a regular file?): ' . $this->filePath
            );
        }

        $file = new \SplFileInfo($this->filePath);

        if (array_key_exists(strtolower($file->getExtension()), $this->extensionMap)) {

            switch ($this->extensionMap[strtolower($file->getExtension())]) {
                case YamlFileLoader::class:
                    return (new YamlFileLoader((string) $file))->load();
                case JsonFileLoader::class:
                    return (new JsonFileLoader((string) $file))->load();
                case PhpFileLoader::class:
                    return (new PhpFileLoader((string) $file))->load();
                case IniFileLoader::class:
                    return (new IniFileLoader((string) $file))->load();
                default:
                    throw new ConfigLoaderException(sprintf(
                        "Error parsing file (no loader for extension '%s'): %s",
                        $file->getExtension(),
                        (string) $file
                    ));
            }
        }
        else {
            return new ConfigValues([]);
        }
    }
}