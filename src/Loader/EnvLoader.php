<?php

namespace Configula\Loader;
use Configula\ConfigValues;
use Dflydev\DotAccessData\Data;

/**
 * Env Loader
 *
 * @package Configula\Loader
 */
class EnvLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var null|string
     */
    private $delimiter;

    /**
     * @var bool
     */
    private $toLower;

    /**
     * Environment Loader Constructor
     *
     * @param string $prefix Specify a prefix, and only environment variables with this prefix will be read
     *                                (e.g. "MYAPP_" means that this will only read env vars starting with "MYAPP_")
     * @param null|string $delimiter Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                                would become the key, "MYSQL.HOST"
     * @param bool $toLower           Convert all keys to lower-case
     */
    public function __construct(string $prefix = '', ?string $delimiter = '_', bool $toLower = true)
    {
        $this->prefix    = $prefix;
        $this->delimiter = $delimiter;
        $this->toLower   = $toLower;
    }

    /**
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $configValues = new Data();

        foreach ($_ENV as $valName => $valVal) {

            if ($this->prefix && $this->prefix != substr($valName, 0, strlen($this->prefix))) {
                continue;
            }

            $valName = ($this->prefix) ? substr($valName, strlen($this->prefix)) : $valName;
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