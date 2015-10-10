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
    /**
     * @var array  Configuration Settings
     */
    private $configSettings = array();

    /**
     * @var int  Iterator Access Counter
     */
    private $iteratorCount = 0;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $configPath An optional absolute path to the configuration folder
     * @param array  $defaults   An optional list of defaults to fall back on, set at instantiation
     */
    public function __construct($configPath = null, $defaults = array())
    {
        //Set the defaults
        $this->configSettings = $defaults;

        //Load the config files
        if ($configPath) {
            //Append trailing slash
            if (substr($configPath, strlen($configPath) - 1) != DIRECTORY_SEPARATOR) {
                $configPath .= DIRECTORY_SEPARATOR;
            }

            $this->loadConfig($configPath);
        }
    }

    // --------------------------------------------------------------

    /**
     * Parse a directory for configuration files and load the files
     *
     * @param  string     $configPath An absolute path to the configuration folder
     * @return int        The number of configuration settings loaded
     * @throws \Exception If cannot read from Configuration Path
     */
    public function loadConfig($configPath)
    {
        //Array to hold the configuration items as we get them
        $config = array();

        //Unparsed local files
        $unparsedLocalFiles = array();

        //Path good?
        if (! is_readable($configPath)) {
            throw new ConfigulaException("Cannot read from config path!  Does it exist?  Is it readable?");
        }

        //Run all of the files in the directory
        foreach (scandir($configPath) as $filename) {
            //If the file ends in .local.EXT, then put it in the files to be processed later
            $ext = pathinfo($configPath.$filename, PATHINFO_EXTENSION);

            if (substr($filename, strlen($filename)-strlen('.local.'.$ext)) == '.local.'.$ext) {
                $unparsedLocalFiles[] = $filename;
            } else {
                $config = $this->mergeConfigArrays($config, $this->parseConfigFile($configPath.$filename));
            }
        }

        //Go back a second time and run all of the .local files
        foreach ($unparsedLocalFiles as $filename) {
            $config = $this->mergeConfigArrays($config, $this->parseConfigFile($configPath.$filename));
        }

        $this->configSettings = $this->mergeConfigArrays($this->configSettings, $config);

        return count($this->configSettings);
    }

    /**
     * Load configuration values from a file
     *
     * @param  string $configFilePath An absolute path to the configuration file
     * @return int    The number of configuration settings loaded
     * @throws \Exception If cannot read from Configuration file
     */
    public function loadConfgFile($configFilePath)
    {
        if (! is_readable($configFilePath)) {
            throw new ConfigulaException("Cannot read config file!  Does it exist?  Is it readable?");
        }

        $config = $this->parseConfigFile($configFilePath);

        $this->configSettings = $this->mergeConfigArrays($this->configSettings, $config);

        return count($this->configSettings);
    }

    // --------------------------------------------------------------

    /**
     * Parse a configuration file
     *
     * @param  string     $filepath The full path to the config file
     * @return array      An array of configuration items, or an empty array if the file could not be parsed
     * @throws \Exception If cannot read from the configuration file
     */
    public function parseConfigFile($filepath)
    {
        if (! is_readable($filepath)) {
            throw new ConfigulaException("Cannot read from the config file: $filepath");
        }

        $parserClassname = 'Configula\\Drivers\\'.ucfirst(strtolower(pathinfo($filepath, PATHINFO_EXTENSION)));

        if (class_exists($parserClassname)) {
            $cls = new $parserClassname();

            return $cls->read($filepath);
        } else {
            return array();
        }
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
        return (isset($this->configSettings[$item])) ? $this->configSettings[$item] : false;
    }

    // --------------------------------------------------------------

    /**
     * Return a configuration item
     *
     * @param  string $item         The configuration item to retrieve
     * @param  mixed  $defaultValue The default value to return for a configuration item if no configuration item exists
     * @return mixed  An array containing all configuration items, or a specific configuration item, or NULL
     */
    public function getItem($item, $defaultValue = null)
    {
        if (isset($this->configSettings[$item])) {

            return $this->configSettings[$item];

        } elseif (strpos($item, '.') !== FALSE) {

            $cs = $this->configSettings;
            if ($val = $this->getNestedVar($cs, $item)) {
                return $val;
            }

        }

        return $defaultValue;
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
            return $this->configSettings;
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
        $vals = array_values($this->configSettings);

        return $vals[$this->iteratorCount];
    }

    public function key()
    {
        $keys = array_keys($this->configSettings);

        return $keys[$this->iteratorCount];
    }

    public function next()
    {
        $this->iteratorCount++;
    }

    public function valid()
    {
        $vals = array_values($this->configSettings);

        return (isset($vals[$this->iteratorCount]));
    }

    /*
     * Count Interface
     */

    public function count()
    {
        return count($this->configSettings);
    }

    // --------------------------------------------------------------

    /**
     * Merge configuration arrays
     *
     * What I would wish that array_merge_recursive actually does...
     * From: http://www.php.net/manual/en/function.array-merge-recursive.php#102379
     *
     * @param  array $arr1 Array #2
     * @param  array $arr2 Array #1
     * @return array
     */
    private function mergeConfigArrays($arr1, $arr2)
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value)) {
                $arr1[$key] = $this->mergeConfigArrays($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
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
