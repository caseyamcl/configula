<?php

namespace Configula;

class Config
{
    /**
     * Configuration settings
     * @var array 
     */
    private $configSettings = array();
    
    // --------------------------------------------------------------
    
    /**
     * Config File Constructor
     * 
     * @param $configPath
     * An optional absolute path to the configuration folder
     * 
     * @param $defaults
     * An optional list of defaults to fall back on, set at instantiation
     * 
     */
    public function __construct($configPath = NULL, $defaults = array())
    {
        //Set the defaults
        $this->configSettings = $defaults;
        
        //Load the config files
        if ($configPath) {

            //Append trailing slash
            if (substr($configPath, strlen($configPath) - 1) != DIRECTORY_SEPARATOR) {
                $configPath .= DIRECTORY_SEPARATOR;
            }

            $this->load_config($configPath); 
        }
    }
    
    // --------------------------------------------------------------

    /**
     * Parse a directory for configuration files and load the files
     * 
     * @param string $configPath 
     * An absolute path to the configuration folder
     * 
     * @return int
     * The number of configuration settings loaded
     */
    public function load_config($configPath)
    {
        //Array to hold the configuration items as we get them
        $config = array();
        
        //Unparsed local files
        $unparsedLocalFiles = array();  
        
        //Path good?
        if ( ! is_readable($configPath))
            throw new \Exception("Cannot read from config path!  Does it exist?  Is it readable?");
        
        //Run all of the files in the directory
        foreach(scandir($configPath) as $filename) {

            //If the file ends in .local.EXT, then put it in the files to be processed later
            $ext = pathinfo($configPath . $filename, PATHINFO_EXTENSION);
            if (substr($filename, strlen($filename)-strlen('.local.' . $ext)) == '.local.' . $ext) {
                $unparsedLocalFiles[] = $filename;
            }
            else {
             $config = $this->mergeConfigArrays($config, $this->parseConfigFile($configPath . $filename));
            }
        }
        
        //Go back a second time and run all of the .local files
        foreach($unparsedLocalFiles as $filename) {
            $config = $this->mergeConfigArrays($config, $this->parseConfigFile($configPath . $filename));
        }
        
        $this->configSettings = $config;
        
        return count($this->configSettings);
    }
    
    // --------------------------------------------------------------
    
    /**
     * Parse a configuration file
     *
     * @param string $filename
     * The full path to the config file
     *
     * @return array
     * An array of configuration items, or an empty array if the file could not be parsed
     */
    public function parseConfigFile($filepath)
    {
        if ( ! is_readable($filepath)) {
            throw new \Exception("Cannot read from the config file: $filepath");
        }

        $parserClassname = 'Configula\\Drivers\\' . ucfirst(strtolower(pathinfo($filepath, PATHINFO_EXTENSION)));

        if (class_exists($parserClassname)) {

            $cls = new $parserClassname();
            return $cls->read($filepath);

        }
        else {
            return array();
        }

    }

    // --------------------------------------------------------------

    /**
     * Magic Method for getting a configuration settings
     * 
     * @param string $item
     * @return mixed
     */
    public function __get($item)
    {    
        return (isset($this->configSettings[$item])) ? $this->configSettings[$item] : FALSE;   
    }
    
    // --------------------------------------------------------------

    /**
     * Return a configuration item
     *  
     * @param string $item 
     * The configuration item to retrieve
     * 
     * @param mixed $defaultValue
     * The default value to return for a configuration item if no configuration item exists
     * 
     * @return mixed
     * An array containing all configuration items, or a specific configuration item
     * NULL if a specified configuration item does not exist
     */
    public function getItem($item, $defaultValue = NULL)
    {
            if (isset($this->configSettings[$item])) {
                return $this->configSettings[$item];
            }
            elseif (strpos($item, '.') !== FALSE) {
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
    public function getItems($items = NULL)
    {
        if ($items) {

            if ( ! is_array($items)) {
                $items = array($items);
            }

            $output = array();
            foreach($items as $item) {
                $output[$item] = $this->getItem($item);
            }

            return $output;
        }
        else {
            return $this->configSettings;
        }
    }

    // --------------------------------------------------------------

    /**
     * Merge configuration arrays
     *
     * What I would wish that array_merge_recursive actually does...
     * From: http://www.php.net/manual/en/function.array-merge-recursive.php#102379
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    private function mergeConfigArrays($arr1, $arr2)
    {
        foreach($arr2 as $key => $value)
        {
            if(array_key_exists($key, $arr1) && is_array($value))
                $arr1[$key] = $this->mergeConfigArrays($arr1[$key], $arr2[$key]);

            else
                $arr1[$key] = $value;

        }

        return $arr1;
    }

    // --------------------------------------------------------------

    /**
     * Get nested variable using dot (val.subval.subsubval) syntax
     *
     * From: http://stackoverflow.com/questions/2286706/php-lookup-array-contents-with-dot-syntax
     *   
     * @param array $context
     * @param string $name
     * @return mixed
     */
    private function getNestedVar(&$context, $name)
    {
        $pieces = explode('.', $name);

        foreach ($pieces as $piece) {
            if ( ! is_array($context) || ! array_key_exists($piece, $context)) {
                // error occurred
                return NULL;
            }
            $context = &$context[$piece];
        }

        return $context;
    }
}

/* EOF: config.php */