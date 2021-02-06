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
use InvalidArgumentException;
use SplFileInfo;

/**
 * Class CascadingLoader
 *
 * @package FandF\Config
 */
class CascadingConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var iterable|ConfigLoaderInterface[]
     */
    private $loaders;

    /**
     *
     * Pass in an iterable list of multiple loaders, file names, or arrays of values
     *
     * @param  iterable|array[]|string[]|SplFileInfo[]|ConfigLoaderInterface[] $items
     * @return CascadingConfigLoader
     */
    public static function build(iterable $items): CascadingConfigLoader
    {
        foreach ($items as $item) {
            switch (true) {
                case $item instanceof ConfigLoaderInterface:
                    $loaders[] = $item;
                    break;
                case is_array($item):
                    $loaders[] = new ArrayValuesLoader($item);
                    break;
                case is_string($item) && file_exists($item):
                    $loaders[] = new FileLoader($item);
                    break;
                case $item instanceof SplFileInfo:
                    $loaders[] = ($item->isDir()) ? new FolderLoader($item) : new FileLoader((string) $item);
                    break;
                default:
                    throw new InvalidArgumentException(sprintf(
                        'Invalid config source (allowed: array, string (filepath), \SplFileInfo, or config loader): %s',
                        gettype($item)
                    ));
            }
        }

        return new static($loaders ?? []);
    }

    /**
     * CascadingLoader constructor.
     *
     * @param iterable|ConfigLoaderInterface[] $loaders Loaders, in the order that you want to load them
     */
    final public function __construct(iterable $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $config = new ConfigValues([]);

        foreach ($this->loaders as $loader) {
            $config = $config->merge($loader->load());
        }

        return $config;
    }
}
