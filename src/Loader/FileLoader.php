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

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use Configula\Exception\UnmappedFileExtensionException;
use Error;
use SplFileInfo;

/**
 * File Loader
 *
 * Strategy class to load a file based on its extension
 *
 * @package Configula\Loader
 */
final class FileLoader implements FileLoaderInterface
{
    public const DEFAULT_EXTENSION_MAP = [
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
     * @param string         $filePath
     * @param array|string[] $extensionMap Keys are case-insensitive extensions; values are class names
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
        $file = new SplFileInfo($this->filePath);

        if (array_key_exists(strtolower($file->getExtension()), $this->extensionMap)) {
            $className = $this->extensionMap[strtolower($file->getExtension())];

            try {
                return (new $className((string) $file->getRealPath()))->load();
            } catch (Error $e) {
                if (! is_a($className, AbstractFileLoader::class, true)) {
                    throw new ConfigLoaderException(
                        sprintf(
                            'File loader class %s must be instance of %s',
                            $className,
                            AbstractFileLoader::class
                        )
                    );
                } else {
                    throw $e;
                }
            }
        } else {
            throw new UnmappedFileExtensionException(
                sprintf(
                    "Error parsing file (no loader for extension '%s'): %s",
                    $file->getExtension(),
                    (string) $file
                )
            );
        }
    }
}
