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
class FolderLoader implements ConfigLoaderInterface
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
     */
    public function __construct(string $path, array $defaultValues = [])
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

        // Check valid path
        if (! is_readable($this->path) OR ! is_dir($this->path)) {
            throw new ConfigLoaderException(
                'Cannot read from folder path (does it exist? is it a directory?): ' . $this->path
            );
        }

        // File list
        $fileList = [];

        /**
         * Build file list
         *
         * If the file has '.local', we should load it later
         * If the file has '.dist', we should load it sooner
         * If the file has neither, we should load it normally
         *
         * @var \SplFileInfo $file
         */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path)) as $file) {

            isset($fileCount) ? $fileCount++ : $fileCount = 0;

            $ext      = strtolower($file->getExtension());
            $basename = rtrim($file->getBasename($ext), '.');

            if (strcasecmp(substr($basename, -6), '.local') == 0) {
                $fileList[3][] = $file;
            }
            elseif (strcasecmp(substr($basename, -5), '.dist') == 0) {
                $fileList[1][] = $file;
            }
            else {
                $fileList[2][] = $file;
            }
        }

        ksort($fileList, SORT_NUMERIC);

        // Iterate over the list and load each file
        foreach ($fileList as $set) {
            /** @var \SplFileInfo $file */
            foreach ($set as $file) {
                if ($newConfig = (new FileLoader((string) $file))->load()) {
                    $config = $config->merge($newConfig);
                }
            }
        }

        return $config;
    }
}