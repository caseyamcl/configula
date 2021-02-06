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
final class ArrayValuesLoader implements ConfigLoaderInterface
{
    /**
     * @var array
     */
    private $configValues;

    /**
     * ArrayValuesLoader constructor.
     *
     * @param array $configValues
     */
    public function __construct(array $configValues)
    {
        $this->configValues = $configValues;
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
