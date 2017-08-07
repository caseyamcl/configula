<?php


namespace Configula;

use Configula\Exception\ConfigException;
use Configula\Util\RecursiveArrayMerger;
use Dflydev\DotAccessData\Data;
use Configula\Exception\ConfigValueNotFoundException;

/**
 * Class ConfigValues
 *
 * @package Configula
 */
class ConfigValues implements \IteratorAggregate, \Countable, \ArrayAccess
{
    const NOT_SET = '__THe_VALUe___iS__Not_SET_l33t__';

    /**
     * @var Data
     */
    private $values;

    /**
     * Construct from other config values
     *
     * @param ConfigValues $configValues
     * @return ConfigValues|static
     */
    public static function fromConfigValues(ConfigValues $configValues)
    {
        return new static($configValues->getArrayCopy());
    }

    /**
     * Config constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = new Data($values);
    }

    /**
     * Magic method - Get a value or path, or throw an exception
     *
     * @param string $path
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
     * @param string $path
     * @return bool
     */
    public function __isset(string $path): bool
    {
        return $this->has($path);
    }

    /**
     * Get a values
     *
     * @param string $path Accepts '.' path notation
     * @param mixed $default
     * @return array|mixed|null
     */
    public function get(string $path, $default = self::NOT_SET)
    {
        $result = $this->values->get($path, $default);

        if ($result === self::NOT_SET) {
            throw new ConfigValueNotFoundException('Config value not found: ' . $path);
        }

        return $result;
    }

    /**
     * Check if value exists (even if it is null)
     *
     * @param string $path
     * @return bool
     */
    public function has(string $path): bool
    {
        $result = $this->values->get($path, self::NOT_SET);
        return $result !== self::NOT_SET;
    }

    /**
     * Check if value exists and has non-empty value
     *
     * Returns FALSE if value is NULL, empty array, or empty string
     *
     * @param string $path
     * @return bool
     */
    public function hasValue(string $path): bool
    {
        $result = $this->values->get($path, null);
        return (! in_array($result, [null, '', []], true));
    }

    /**
     * @return \RecursiveArrayIterator|iterable
     */
    public function getIterator(): iterable
    {
        return new \RecursiveArrayIterator($this->values->export());
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return $this->values->export();
    }

    /**
     * Merge and return an new Config instance
     *
     * @param ConfigValues $config
     * @return static|ConfigValues
     */
    public function merge(ConfigValues $config): ConfigValues
    {
        return new static(RecursiveArrayMerger::merge(
            $this->values->export(),
            $config->getArrayCopy()
        ));
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        throw new ConfigException("Cannot set config value: " . $offset . "; config values are immutable");
    }

    /**
     *
     */
    public function offsetUnset($offset)
    {
        throw new ConfigException("Cannot unset config value: " . $offset . "; config values are immutable");
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
        return $this->getIterator()->count();
    }
}