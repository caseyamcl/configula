<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 5
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

use CallbackFilterIterator;
use Configula\ConfigLoaderInterface;
use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use Configula\Util\LocalDistFileIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Config Folder Files Loader
 *
 * This provides v2.x functionality/compatibility to v3.x/v4.x
 *
 * Loads all known
 *
 * @package Configula\Loader
 */
final readonly class FolderLoader implements ConfigLoaderInterface
{
    /**
     * ConfigFolderFilesLoader constructor.
     *
     * @param string|SplFileInfo $path
     * @param bool $recursive
     */
    public function __construct(
        private string|SplFileInfo $path,
        private bool $recursive = true
    ) {
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        // Check valid path
        if (! is_readable($this->path) or ! is_dir($this->path)) {
            throw new ConfigLoaderException(
                'Cannot read from folder path (does it exist? is it a directory?): ' . $this->path
            );
        }

        // Build either a recursive directory iterator a single directory (files only) iterator
        $innerIterator = $this->recursive
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path))
            : new CallbackFilterIterator(new FilesystemIterator($this->path), function (SplFileInfo $info) {
                return $info->isFile();
            });

        // Use FileListLoader to load configuration respecting local/dist extension ordering
        return (new FileListLoader(new LocalDistFileIterator($innerIterator)))->load();
    }
}
