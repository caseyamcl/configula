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

namespace Configula;

use ArrayAccess;
use Configula\Exception\ConfigLogicException;
use Configula\Util\ArrayUtils;
use Countable;
use Dflydev\DotAccessData\Data;
use Configula\Exception\ConfigValueNotFoundException;
use IteratorAggregate;
use ReturnTypeWillChange;

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

    private Data $values;

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
     * Get a value
     *
     * @param string $path Accepts '.' path notation for nested values
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, mixed $default = self::NOT_SET): mixed
    {
        return match (true) {
            // Check for top-level, even if it has a dot in the name
            isset($this->values->export()[$path]) => $this->values->export()[$path],

            // Use default dot-notation behavior for dot-access-data
            $this->values->has($path) => $this->values->get($path),

            // Return default if specified
            $default !== static::NOT_SET => $default,

            // Give up
            default => throw new ConfigValueNotFoundException('Config value not found: ' . $path),
        };
    }

    /**
     * Find a configuration value, or return NULL if not found
     *
     * This is different from the get() method in that it will not throw an exception if the value doesn't exist.
     *
     * @param  string $path Accepts '.' path notation for nested values
     */
    public function find(string $path): mixed
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
     * @return mixed
     */
    public function __invoke(string $path, string $default = self::NOT_SET): mixed
    {
        return $this->get($path, $default);
    }

    // --------------------------------------------------------------
    // Merging ConfigValues

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
    // Iterating and counting interfaces

    /**
     * Iterator access
     *
     * Flattens the structure and implodes paths
     */
    #[ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return ArrayUtils::flattenAndIterate($this->getArrayCopy());
    }

    /**
     * Array-access to check if configuration value exists
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Array-access to a configuration value
     */
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Always throw exception.  Cannot set config values in immutable object
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new ConfigLogicException("Cannot set config value: " . $offset . "; config values are immutable");
    }

    /**
     * Always throw exception.  Cannot set config values in immutable object
     */
    public function offsetUnset(mixed $offset): void
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
