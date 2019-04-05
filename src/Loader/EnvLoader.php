<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Filter\ExtractTopLevelItemFilter;
use Dflydev\DotAccessData\Data;

/**
 * Env Loader
 *
 * Loads configuration from each environment variable
 *
 * @package Configula\Loader
 */
class EnvLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $regex;

    /**
     * @var null|string
     */
    private $delimiter;

    /**
     * @var bool
     */
    private $toLower;

    /**
     * Load from environment looking only for those values with a specified prefix (and remove prefix)
     *
     * @param string $prefix Specify a prefix, and only environment variables with this prefix will be read
     *                        (e.g. "MYAPP_" means that this will only read env vars starting with "MYAPP_")'
     *                        Values will be
     * @param null|string $delimiter  Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                                would become the key, "MYSQL.HOST" (empty string to not delimit)
     * @param bool $toLower  Convert all keys to lower-case
     * @return ConfigValues
     */
    public static function loadUsingPrefix(string $prefix, string $delimiter = '', bool $toLower = false)
    {
        $prefix = preg_quote($prefix);
        $envLoader = new static("/^{$prefix}/", $delimiter, $toLower);
        $values = $envLoader->load();
        return (new ExtractTopLevelItemFilter($toLower ? strtolower($prefix) : $prefix))->__invoke($values);
    }

    /**
     * Environment Loader Constructor
     *
     * @param string $regex           Optionally filter values based on some regex pattern
     * @param null|string $delimiter  Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                                would become the key, "MYSQL.HOST" (empty string to not delimit)
     * @param bool $toLower  Convert all keys to lower-case
     */
    public function __construct(string $regex = '', string $delimiter = '', bool $toLower = false)
    {
        $this->regex     = $regex;
        $this->delimiter = $delimiter;
        $this->toLower   = $toLower;
    }

    /**
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $configValues = new Data();

        foreach (getenv() as $valName => $valVal) {

            if ($this->regex && ! preg_match($this->regex, $valName)) {
                continue;
            }

            $valName = ($this->delimiter) ? str_replace($this->delimiter, '.', $valName) : $valName;
            $valName = ($this->toLower) ? strtolower($valName) : $valName;
            $configValues->set($valName, $this->prepareVal($valVal));
        }

        return new ConfigValues($configValues->export());
    }

    /**
     * Prepare string value
     *
     * @param mixed $value
     * @return mixed
     */
    private function prepareVal($value)
    {
        if (is_string($value)) {
            switch (strtolower($value)) {
                case 'null':
                    return null;
                case 'false':
                    return false;
                case 'true':
                    return true;
                default:
                    return $value;
            }
        }
        else {
            return $value;
        }
    }
}