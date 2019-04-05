<?php


namespace Configula\Filter;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * Extract items from given top-level item and elevate them to the top level
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ExtractTopLevelItemFilter
{
    /**
     * @var string
     */
    private $topLevelItemName;

    /**
     * Extract Top Level Item constructor.
     *
     * @param string $topLevelItemName
     */
    public function __construct(string $topLevelItemName)
    {
        $this->topLevelItemName = $topLevelItemName;
    }

    /**
     * Remove top-level item and elevate its children to the top level
     *
     * @param ConfigValues $values
     * @return ConfigValues
     */
    public function __invoke(ConfigValues $values): ConfigValues
    {
        if ($values->hasValue($this->topLevelItemName)) {
            $items = $values->getArrayCopy();

            // Elevate all of the sub-items from the array that are children of the prefix to remove
            foreach ($items[$this->topLevelItemName] as $k => $v) {

                // If item already exists in root node, throw exception
                if (isset($items[$k])) {
                    throw new ConfigLoaderException(sprintf(
                        'Name collision (%s) when removing %s.%s from config values',
                        $k,
                        $this->topLevelItemName,
                        $k
                    ));
                }

                $items[$k] = $v;
            }

            // Unset the original prefix item
            unset($items[$this->topLevelItemName]);
            return new ConfigValues($items);
        } else {
            // nothing to do
            return $values;
        }
    }
}