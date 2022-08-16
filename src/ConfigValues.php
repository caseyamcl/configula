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

namespace Configula;

use ArrayAccess;
use Configula\Exception\ConfigLogicException;
use Configula\Util\ArrayUtils;
use Countable;
use Dflydev\DotAccessData\Data;
use Configula\Exception\ConfigValueNotFoundException;
use IteratorAggregate;
use ReturnTypeWillChange;
use Traversable;

/**
 * Config Values Class
 *
 * Immutable class for storing configuration values
 *
 * @package Configula
 */
class ConfigValues implements IteratorAggregate, Countable, ArrayAccess
{
    // Silly value, but we need one to reasonably ensure there is no collision with actual data
    public const NOT_SET = '__THe_VALUe___iS__Not_SET_l33t__';

    /**
     * @var Data
     */
    private $values;

    /**
     * Construct from other config values
     *
     * @param  ConfigValues $configValues
     * @return ConfigValues|static
     */
    public static function fromConfigValues(ConfigValues $configValues): self
    {
        return new static($configValues->getArrayCopy());
    }

    /**
     * Config constructor.
     *
     * @param array $values
     */
    final public function __construct(array $values)
    {
        $this->values = new Data($values);
        $this->init();
    }

    /**
     * Get a values
     *
     * @param  string $path    Accepts '.' path notation for nested values
     * @param  mixed  $default
     * @return array|mixed|null
     */
    public function get(string $path, $default = self::NOT_SET)
    {
        switch (true) {
            // Check for top-level, even if it has a dot in the name
            case isset($this->values->export()[$path]):
                return $this->values->export()[$path];

            // Use default dot-notation behavior for dot-access-data
            case $this->values->has($path):
                return $this->values->get($path);

            // Return default if specified
            case $default !== static::NOT_SET:
                return $default;

            // Give up
            default:
                throw new ConfigValueNotFoundException('Config value not found: ' . $path);
        }
    }

    /**
     * Find a configuration value, or return NULL if not found
     *
     * This is different from the get() method in that it will not throw an exception if the value doesn't exist.
     *
     * @param  string $path Accepts '.' path notation for nested values
     * @return array|mixed|null
     */
    public function find(string $path)
    {
        return $this->get($path, null);
    }

    /**
     * Check if value exists (even if it is NULL)
     *
     * @param  string $path
     * @return bool
     */
    public function has(string $path): bool
    {
        return isset($this->values->export()[$path]) || $this->values->has($path);
    }

    /**
     * Check if value exists and has non-empty value
     *
     * Returns FALSE if value is NULL, empty array, or empty string
     *
     * @param  string $path
     * @return bool
     */
    public function hasValue(string $path): bool
    {
        $result = $this->get($path, null);
        return (! in_array($result, [null, '', []], true));
    }

    /**
     * Get an array copy of config values
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        return $this->values->export();
    }

    // --------------------------------------------------------------
    // Magic Method Access

    /**
     * Magic method - Get a value or path, or throw an exception
     *
     * @param  string $path Accepts '.' path notation for nested values
     * @return mixed
     * @throws ConfigValueNotFoundException  If the config value is not found
     */
    public function __get(string $path)
    {
        return $this->get($path);
    }

    /**
     * Magic method - Check if a value or path exists
     *
     * @param  string $path Accepts '.' path notation for nested values
     * @return bool
     */
    public function __isset(string $path): bool
    {
        return $this->has($path);
    }

    /**
     * Magic method - Get a value or path, or throw an exception
     *
     * @param string $path    Accepts '.' path notation for nested values
     * @param string $default
     * @return array|mixed|null
     */
    public function __invoke(string $path, string $default = self::NOT_SET)
    {
        return $this->get($path, $default);
    }

    // --------------------------------------------------------------
    // Merging

    /**
     * Merge config values and return a new Config instance
     *
     * This is a recursive merge.  Any sub-arrays will be cascade-merged.
     *
     * @param  ConfigValues $config
     * @return static|ConfigValues
     */
    public function merge(ConfigValues $config): ConfigValues
    {
        return new static(ArrayUtils::merge($this->getArrayCopy(), $config->getArrayCopy()));
    }

    /**
     * Merge values and return a new Config instance
     *
     * This is a recursive merge.  Any sub-arrays will be cascade-merged.
     *
     * @param  array $values
     * @return static|ConfigValues
     */
    public function mergeValues(array $values): ConfigValues
    {
        return $this->merge(new ConfigValues($values));
    }

    // --------------------------------------------------------------
    // Deprecated methods

    /**
     *
     * @param      string $path
     * @param      string|null $default
     * @return     array|mixed|null
     *@deprecated use get() or find() instead
     */
    public function getItem(string $path, ?string $default = null)
    {
        @trigger_error(
            'ConfigValues::getItem() is deprecated since version 3.0 and will be removed in 4.0. '
            . 'Use ConfigValues::get() or ConfigValues::find() instead.',
            E_USER_DEPRECATED
        );

        return $this->get($path, $default);
    }

    /**
     * Get an array copy of config values
     *
     * @deprecated Use getArrayCopy instead
     * @return     array
     */
    public function getItems(): array
    {
        @trigger_error(
            'ConfigValues::getItems() is deprecated since version 3.0 and will be removed in 4.0. '
            . 'Use ConfigValues::getArrayCopy() instead.',
            E_USER_DEPRECATED
        );

        return $this->getArrayCopy();
    }

    /**
     * Check if a value exists
     *
     * @deprecated Use has() instead
     * @param      string $path
     * @return     bool
     */
    public function valid(string $path): bool
    {
        @trigger_error(
            'ConfigValues::valid() is deprecated since version 3.0 and will be removed in 4.0. '
            . 'Use ConfigValues::has() instead.',
            E_USER_DEPRECATED
        );

        return $this->has($path);
    }

    // --------------------------------------------------------------
    // Iterating and counting interfaces

    /**
     * Iterator access
     *
     * Flattens the structure and implodes paths
     *
     * @return iterable|array|Traversable|array
     */
    #[ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return ArrayUtils::flattenAndIterate($this->getArrayCopy());
    }

    /**
     * Array-access to check if configuration value exists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Array-access to a configuration value
     *
     * @param  mixed $offset
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Always throw exception.  Cannot set config values in immutable object
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new ConfigLogicException("Cannot set config value: " . $offset . "; config values are immutable");
    }

    /**
     * Always throw exception.  Cannot set config values in immutable object
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new ConfigLogicException("Cannot unset config value: " . $offset . "; config values are immutable");
    }

    /**
     * Count values
     *
     * Counts all paths (not just the top-level paths)
     *
     * @return int
     */
    public function count(): int
    {
        return count(iterator_to_array($this->getIterator()));
    }

    /**
     * Do nothing by default.
     *
     * This is to account for the fact that the `__construct()` method is now final, so
     * additional logic should go in here.
     */
    protected function init(): void
    {
        // pass..
    }
}
