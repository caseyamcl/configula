<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * Config Folder Files Loader
 *
 * This provides v2.x functionality/compatibility to v3.x
 *
 * @package Configula\Loader
 */
class ConfigFolderFilesLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $defaults;

    /**
     * ConfigFolderFilesLoader constructor.
     *
     * @param string $path
     * @param array $defaultValues
     * @param null $extensionMap
     */
    public function __construct(string $path, array $defaultValues = [], $extensionMap = FileLoader::USE_DEFAULT)
    {
        $this->path     = $path;
        $this->defaults = $defaultValues;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        // Config values
        $config = new ConfigValues($this->defaults);

        // Save '*.local.*' for the end..
        /** @var array|\SplFileInfo[] $localFiles */
        $localFiles = [];

        // Check valid path
        if (! is_readable($this->path) OR ! is_dir($this->path)) {
            throw new ConfigLoaderException(
                'Cannot read from config folder path (does it exist? is it a directory?): ' . $this->path
            );
        }

        // Iterate over the files in the directory and load each one
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path));

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {

            $ext      = strtolower($file->getExtension());
            $basename = rtrim($file->getBasename($ext), '.');

            if (strcasecmp(substr($basename, -6), '.local') == 0) {
                $localFiles[] = $file;
            }
            elseif ($newConfig = (new FileLoader((string) $file))->load()) {
                $config = $config->merge($newConfig);
            }
        }

        foreach ($localFiles as $file) {
            if ($newConfig = (new FileLoader((string) $file))->load()) {
                $config = $config->merge($newConfig);
            }
        }

        return $config;
    }
}