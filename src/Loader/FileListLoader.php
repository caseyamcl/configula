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

namespace Configula\Loader;

use Configula\ConfigLoaderInterface;
use Configula\ConfigValues;
use Configula\Exception\ConfigFileNotFoundException;
use Configula\Exception\ConfigLoaderException;
use Configula\Exception\UnmappedFileExtensionException;
use SplFileInfo;

/**
 * Loader that loads from files, but ignores missing/unmapped files
 *
 * @package Configula\Loader
 */
final class FileListLoader implements ConfigLoaderInterface
{
    /**
     * @var iterable|SplFileInfo[]|string[]
     */
    private $files;

    /**
     * @var array
     */
    private $extensionMap;

    /**
     * FileListLoader constructor.
     *
     * @param iterable|SplFileInfo[]|string[] $files
     * @param array                           $extensionMap
     */
    public function __construct(iterable $files, array $extensionMap = FileLoader::DEFAULT_EXTENSION_MAP)
    {
        $this->files = $files;
        $this->extensionMap = $extensionMap;
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
            $fileInfo = ($file instanceof SplFileInfo) ? $file : new SplFileInfo($file);
            try {
                $newValues = (new FileLoader((string) $fileInfo->getRealPath(), $this->extensionMap))->load();
                $values = $values->merge($newValues);
            } catch (ConfigFileNotFoundException | UnmappedFileExtensionException $e) {
                // pass..
            }
        }

        return $values;
    }
}
