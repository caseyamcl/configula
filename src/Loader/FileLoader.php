<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use Error;
use SplFileInfo;

/**
 * File Loader
 *
 * Strategy class to
 *
 * @package Configula\Loader
 */
class FileLoader implements FileLoaderInterface
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
     * @param array|string[] $extensionMap  Keys are case-insensitive extensions; values are class names
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
     * @throws ConfigLoaderException  If file is missing, not readable, or if no registered loader for file extension
     */
    public function load(): ConfigValues
    {
        // Check valid path
        if (! is_readable($this->filePath) OR ! is_file($this->filePath)) {
            throw new ConfigLoaderException(
                'Cannot read from file path (does it exist? is it a regular file?): ' . $this->filePath
            );
        }

        $file = new SplFileInfo($this->filePath);

        if (array_key_exists(strtolower($file->getExtension()), $this->extensionMap)) {
            $extension = strtolower($file->getExtension());

            if (isset($this->extensionMap[$extension])) {
                $className = $this->extensionMap[strtolower($file->getExtension())];

                try {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return (new $className($file->getRealPath()))->load();
                } catch (Error $e) {
                    if (! is_a(AbstractFileLoader::class, $className, true)) {
                        throw new ConfigLoaderException(sprintf(
                            'File loader class %s must be instance of %s',
                            $className,
                            FileLoaderInterface::class
                        ));
                    } else {
                        throw $e;
                    }
                }
            } else {
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