<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 4
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Configula;

use Configula\Exception\ConfigFileNotFoundException;
use Configula\Loader\CascadingConfigLoader;
use Configula\Loader\EnvLoader;
use Configula\Loader\FileListLoader;
use Configula\Loader\FolderLoader;
use Configula\Loader\FileLoader;
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
     * @param  iterable|array[]|string[]|SplFileInfo[]|ConfigLoaderInterface[] $items
     * @return ConfigValues
     */
    public static function loadMultiple(iterable $items): ConfigValues
    {
        return CascadingConfigLoader::build($items)->load();
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
        return (new ConfigValues($defaults))->merge((new FolderLoader($configDirectory, false))->load());
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
        } elseif (is_file($configPath)) { // Elseif if file, then just load that single file...
            $pathValues = (new FileLoader($configPath))->load();
        } elseif ($configPath === '') { // Else, no path provided, so empty values
            $pathValues = new ConfigValues([]);
        } else {
            throw new ConfigFileNotFoundException('Cannot resolve config path: ' . $configPath);
        }

        // Merge defaults and return
        return (new ConfigValues($defaults))->merge($pathValues);
    }

    /**
     * Load from environment looking only for those values with a specified prefix (and remove prefix)
     *
     * @param  string      $prefix    Specify a prefix, and only environment variables with this prefix will be read
     *                                (e.g. "MYAPP_" means that this will only read env vars starting with
     *                                "MYAPP_")' Values will be
     * @param  string      $delimiter Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                                would become the key, "MYSQL.HOST" (empty string to not delimit)
     * @param  bool        $toLower   Convert all keys to lower-case
     *
     * @return ConfigValues
     */
    public static function loadEnv(string $prefix = '', string $delimiter = '', bool $toLower = false): ConfigValues
    {
        return ($prefix)
            ? EnvLoader::loadUsingPrefix($prefix, $delimiter, $toLower)
            : (new EnvLoader('', $delimiter, $toLower))->load();
    }

    /**
     * Load configuration from environment variables using regex
     *
     * @param string      $regex     Optionally filter values based on some regex pattern
     * @param string      $delimiter Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                               would become the key, "MYSQL.HOST" (empty string to not delimit)
     * @param bool        $toLower   Convert all keys to lower-case
     * @return ConfigValues
     */
    public static function loadEnvRegex(string $regex, string $delimiter = '', bool $toLower = false): ConfigValues
    {
        return (new EnvLoader($regex, $delimiter, $toLower))->load();
    }
}
