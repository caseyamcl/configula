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

namespace Configula\Filter;

use Configula\ConfigValues;

/**
 * Class RemovePrefixFilter
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class RemovePrefixFilter
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * Extract Top Level Item constructor.
     *
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param  ConfigValues $values
     * @return ConfigValues
     */
    public function __invoke(ConfigValues $values): ConfigValues
    {
        $newValues = [];
        $pattern = sprintf('/^%s/', preg_quote($this->prefix));

        foreach ($values->getArrayCopy() as $name => $val) {
            $newValues[preg_replace($pattern, '', $name)] = $val;
        }

        return new ConfigValues($newValues);
    }
}
