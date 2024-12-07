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

/**
 * Class ArrayValuesLoader
 *
 * @package Configula\Loader
 */
final readonly class ArrayValuesLoader implements ConfigLoaderInterface
{
    /**
     * ArrayValuesLoader constructor.
     *
     * @param array $configValues
     */
    public function __construct(
        private array $configValues
    ) {
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        return new ConfigValues($this->configValues);
    }
}
