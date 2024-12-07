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

use Configula\ConfigLoaderInterface;
use Configula\ConfigValues;
use InvalidArgumentException;
use SplFileInfo;

readonly class CascadingConfigLoader implements ConfigLoaderInterface
{
    /**
     * Pass in an iterable list of multiple loaders, file names, or arrays of values
     *
     * @param  iterable<array|string|SplFileInfo|ConfigLoaderInterface> $items
     * @return CascadingConfigLoader
     */
    public static function build(iterable $items): CascadingConfigLoader
    {
        foreach ($items as $item) {
            $loaders[] = match (true) {
                $item instanceof ConfigLoaderInterface => $item,
                is_array($item) => new ArrayValuesLoader($item),
                is_string($item) && file_exists($item) => new FileLoader($item),
                $item instanceof SplFileInfo => ($item->isDir()) ? new FolderLoader($item) : new FileLoader(
                    (string)$item
                ),
                default => throw new InvalidArgumentException(
                    sprintf(
                        'Invalid config source (allowed: array, string (filepath), \SplFileInfo, or config loader): %s',
                        gettype($item)
                    )
                ),
            };
        }

        return new static($loaders ?? []);
    }

    /**
     * CascadingLoader constructor.
     *
     * @param iterable<ConfigLoaderInterface> $loaders Loaders, in the order that you want to load them
     */
    final public function __construct(
        private iterable $loaders
    ) {
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
