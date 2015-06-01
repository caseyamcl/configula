<?php

/**
 * Configula - A simple configuration tool
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * ------------------------------------------------------------------
 */

namespace Configula;

use ArrayAccess;
use Configula\Exception\ConfigulaException;
use Configula\Exception\NonExistentConfigValueException;
use Iterator;
use Countable;

/**
 * Config Class
 *
 * @package Configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Config implements ArrayAccess, Iterator, Countable
{
    const NO_DEFAULT = '__CONFIGULA_CONFIG_NO_DEFAULT__';

    // ---------------------------------------------------------------

    /**
     * @var array  Configuration Settings
     */
    private $values = array();

    /**
     * @var int  Iterator Access Counter
     */
    private $iteratorCount = 0;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        //Set the defaults
        $this->values = $values;
    }

    // --------------------------------------------------------------

    /**
     * Magic Method for getting a configuration settings
     *
     * @param  string $item The item to get
     * @return mixed  The value
     */
    public function __get($item)
    {
        return (isset($this->values[$item])) ? $this->values[$item] : false;
    }

    // ---------------------------------------------------------------

    /**
     * Return a configuration item
     *
     * Alias of `self::getItem()`
     *
     * @param  string $item         The configuration item to retrieve
     * @param  mixed  $defaultValue The default value to return for a configuration item if no configuration item exists
     * @return mixed  An array containing all configuration items, or a specific configuration item, or NULL
     */
    public function get($item, $defaultValue = self::NO_DEFAULT)
    {
        return $this->getItem($item, $defaultValue);
    }

    // --------------------------------------------------------------

    /**
     * Return a configuration item
     *
     * @param  string $item         The configuration item to retrieve
     * @param  mixed  $defaultValue The default value to return for a configuration item if no configuration item exists
     * @return mixed  An array containing all configuration items, or a specific configuration item, or NULL
     */
    public function getItem($item, $defaultValue = self::NO_DEFAULT)
    {
        if (array_key_exists($item, $this->values)) {
            return $this->values[$item];
        }
        elseif ($defaultValue !== self::NO_DEFAULT) {
            return $defaultValue;
        }
        elseif (strpos($item, '.') !== FALSE) {

            $cs = $this->values;
            if ($val = $this->getNestedVar($cs, $item)) {
                return $val;
            }
        }

        throw new NonExistentConfigValueException("Could not find configuration item: " . $item);
    }

    // --------------------------------------------------------------

    /**
     * Returns configuration items (or all items) as an array
     *
     * @param  string|array   Array of items or single item
     * @return array
     */
    public function getItems($items = null)
    {
        if ($items) {
            if (! is_array($items)) {
                $items = array($items);
            }

            $output = array();
            foreach ($items as $item) {
                $output[$item] = $this->getItem($item);
            }

            return $output;
        } else {
            return $this->values;
        }
    }

    // --------------------------------------------------------------

    /*
     * ArrayAccess Interface
     */
    public function offsetSet($offset, $data)
    {
        throw new ConfigulaException("Configuration is immutable!");
    }

    public function offsetUnset($offset)
    {
        throw new ConfigulaException("Configuration is immutable!");
    }

    public function offsetExists($offset)
    {
        return $this->getItem($offset) ? true : false;
    }

    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }

    /*
     * Iterator Interface
     */
    public function rewind()
    {
        $this->iteratorCount = 0;
    }

    public function current()
    {
        $vals = array_values($this->values);

        return $vals[$this->iteratorCount];
    }

    public function key()
    {
        $keys = array_keys($this->values);

        return $keys[$this->iteratorCount];
    }

    public function next()
    {
        $this->iteratorCount++;
    }

    public function valid()
    {
        $vals = array_values($this->values);

        return (isset($vals[$this->iteratorCount]));
    }

    /*
     * Count Interface
     */

    public function count()
    {
        return count($this->values);
    }

    // --------------------------------------------------------------

    /**
     * Get nested variable using dot (val.subval.subsubval) syntax
     *
     * From: http://stackoverflow.com/questions/2286706/php-lookup-array-contents-with-dot-syntax
     *
     * @param  array  $context
     * @param  string $name
     * @return mixed
     */
    private function getNestedVar(&$context, $name)
    {
        $pieces = explode('.', $name);

        foreach ($pieces as $piece) {
            if (! is_array($context) || ! array_key_exists($piece, $context)) {
                // error occurred
                return NULL;
            }
            $context = &$context[$piece];
        }

        return $context;
    }
}

/* EOF: config.php */
