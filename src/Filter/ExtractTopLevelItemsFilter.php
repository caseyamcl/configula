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
use Configula\Exception\ConfigLoaderException;

/**
 * Extract items from given top-level item and elevate them to the top level
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ExtractTopLevelItemsFilter
{
    /**
     * @var string
     */
    private $topLevelItemName;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * Extract Top Level Item constructor.
     *
     * @param string $topLevelItemName
     * @param string $delimiter
     */
    public function __construct(string $topLevelItemName, string $delimiter = '')
    {
        $this->topLevelItemName = $topLevelItemName;
        $this->delimiter = $delimiter;
    }

    /**
     * Remove top-level item and elevate its children to the top level
     *
     * @param  ConfigValues $values
     * @return ConfigValues
     */
    public function __invoke(ConfigValues $values): ConfigValues
    {
        $items = $values->getArrayCopy();
        $delimiterParts = $this->delimiter
            ? array_filter(explode($this->delimiter, $this->topLevelItemName))
            : [$this->topLevelItemName];

        while ($current = array_shift($delimiterParts)) {
            $items = $this->elevate($items, $current);
        }

        return new ConfigValues($items);
    }

    /**
     * @param array $items
     * @param string $key
     * @return array
     */
    private function elevate(array $items, string $key): array
    {
        if (! isset($items[$key]) or ! is_array($items[$key])) {
            return $items;
        }

        // Elevate all the sub-items from the array that are children of the prefix to remove
        foreach ($items[$key] as $k => $v) {
            // If item already exists in root node, throw exception
            if (isset($items[$k])) {
                throw new ConfigLoaderException(
                    sprintf(
                        'Name collision (%s) when removing %s.%s from config values',
                        $k,
                        $this->topLevelItemName,
                        $k
                    )
                );
            }

            $items[$k] = $v;
        }

        unset($items[$key]);
        return $items;
    }
}
