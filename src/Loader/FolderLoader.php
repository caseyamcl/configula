<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Config Folder Files Loader
 *
 * This provides v2.x functionality/compatibility to v3.x
 *
 * Loads all known
 *
 * @package Configula\Loader
 */
class FolderLoader implements ConfigLoaderInterface
{
    protected const DIST_FILES = 1;
    protected const NORMAL_FILES = 2;
    protected const LOCAL_FILES = 3;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var array
     */
    private $extensionMap;

    /**
     * ConfigFolderFilesLoader constructor.
     *
     * @param string $path
     * @param array $defaultValues
     * @param array $extensionMap
     */
    public function __construct(
        string $path,
        array $defaultValues = [],
        array $extensionMap = DecidingFileLoader::DEFAULT_EXTENSION_MAP
    ) {
        $this->path     = $path;
        $this->defaults = $defaultValues;
        $this->extensionMap = $extensionMap;
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
         * @var SplFileInfo $file
         */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path)) as $file) {

            isset($fileCount) ? $fileCount++ : $fileCount = 0;
            $basename = rtrim($file->getBasename(strtolower($file->getExtension())), '.');

            if (strcasecmp(substr($basename, -6), '.local') == 0) {
                $fileList[self::LOCAL_FILES][] = $file;
            }
            elseif (strcasecmp(substr($basename, -5), '.dist') == 0) {
                $fileList[self::DIST_FILES][] = $file;
            }
            else {
                $fileList[self::NORMAL_FILES][] = $file;
            }
        }

        ksort($fileList, SORT_NUMERIC);

        // Iterate over 'dist', regular, and 'local', in that order
        foreach ($fileList as $set => $files) {
            $config = $config->merge((new FileListLoader($files, $this->extensionMap))->load());
        }

        return $config;
    }
}