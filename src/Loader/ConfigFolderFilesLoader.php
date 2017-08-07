<?php

namespace Configula\Loader;
use Configula\ConfigValues;
use Configula\Exception\ConfigParseException;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Config Folder Files Loader
 *
 * This provides v2.x functionality/compatibility to v3.x
 *
 * @package Configula\Loader
 */
class ConfigFolderFilesLoader implements ConfigLoaderInterface
{
    const USE_DEFAULT = null;

    /**
     * @var array  Keys are extensions (lower-case), values are loader classes
     */
    private $extensionMap = [
        'yml'  => YamlFileLoader::class,
        'yaml' => YamlFileLoader::class,
        'json' => JsonFileLoader::class,
        'php'  => PhpFileLoader::class,
        'ini'  => IniFileLoader::class
    ];

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
     * @param array $defaults
     * @param null $extensionMap
     */
    public function __construct(string $path, array $defaults = [], $extensionMap = self::USE_DEFAULT)
    {
        $this->path         = $path;
        $this->defaults     = $defaults;
        $this->extensionMap = $extensionMap ?: $this->extensionMap;
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
            throw new ParseException(
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
            elseif ($newConfig = $this->loadFile($file)) {
                $config = $config->merge($newConfig);
            }
        }

        foreach ($localFiles as $file) {
            if ($newConfig = $this->loadFile($file)) {
                $config = $config->merge($newConfig);
            }
        }

        return $config;
    }

    /**
     * @param \SplFileInfo $file
     * @return ConfigValues|null
     */
    private function loadFile(\SplFileInfo $file): ?ConfigValues
    {
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
                    throw new ConfigParseException("Error parsing file (could not resolve loader): " . (string) $file);
            }
        }
        else {
            return null;
        }
    }
}