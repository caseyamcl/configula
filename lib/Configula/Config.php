<?php

namespace Configula;

class Config
{
  /**
   * Configuration settings
   * @var array 
   */
  private $config_settings = array();
  
  // --------------------------------------------------------------
  
  /**
   * Config File Constructor
   * 
   * @param $config_path
   * An optional absolute path to the configuration folder
   * 
   * @param $defaults
   * An optional list of defaults to fall back on, set at instantiation
   * 
   */
  public function __construct($config_path = NULL, $defaults = array()) {

    //Set the defaults
    $this->config_settings = $defaults;
    
    //Load the config files
    if ($config_path) {

	    //Append trailing slash
	    if (substr($config_path, strlen($config_path) - 1) != DIRECTORY_SEPARATOR) {
	      $config_path .= DIRECTORY_SEPARATOR;
	    }

      $this->load_config($config_path); 
    }
  }
  
  // --------------------------------------------------------------

  /**
   * Parse a directory for configuration files and load the files
   * 
   * @param string $config_path 
   * An absolute path to the configuration folder
   * 
   * @return int
   * The number of configuration settings loaded
   */
  public function load_config($config_path)
  {
    //Array to hold the configuration items as we get them
    $config = array();
    
    //Unparsed local files
    $unparsed_local_files = array();  
    
    //Path good?
    if ( ! is_readable($config_path))
      throw new \Exception("Cannot read from config path!  Does it exist?  Is it readable?");
    
    //Run all of the files in the directory
    foreach(scandir($config_path) as $filename) {

      //If the file ends in .local.EXT, then put it in the files to be processed later
      $ext = pathinfo($config_path . $filename, PATHINFO_EXTENSION);
      if (substr($filename, strlen($filename)-strlen('.local.' . $ext)) == '.local.' . $ext) {
        $unparsed_local_files[] = $filename;
      }
      else {
    	 $config = $this->merge_config_arrays($config, $this->parse_config_file($config_path . $filename));
      }
    }
    
    //Go back a second time and run all of the .local files
    foreach($unparsed_local_files as $filename) {
			$config = $this->merge_config_arrays($config, $this->parse_config_file($config_path . $filename));
    }
    
    $this->config_settings = $config;
    
    return count($this->config_settings);
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
  public function parse_config_file($filepath) {

  	if ( ! is_readable($filepath)) {
  		throw new \Exception("Cannot read from the config file: $filepath");
  	}

		$parser_classname = 'Configula\\Drivers\\' . ucfirst(strtolower(pathinfo($filepath, PATHINFO_EXTENSION)));

		if (class_exists($parser_classname)) {

			$cls = new $parser_classname();
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
  public function __get($item) {
    
    return (isset($this->config_settings[$item])) ? $this->config_settings[$item] : FALSE;
    
  }
  
  // --------------------------------------------------------------

  /**
   * Return a configuration item
   *  
   * @param string $item 
   * The configuration item to retrieve
   * 
   * @param mixed $default_value
   * The default value to return for a configuration item if no configuration item exists
   * 
   * @return mixed
   * An array containing all configuration items, or a specific configuration item
   * NULL if a specified configuration item does not exist
   */
  public function get_item($item, $default_value = NULL) {

      if (isset($this->config_settings[$item])) {
        return $this->config_settings[$item];
      }
      elseif (strpos($item, '.') !== FALSE) {
        $cs = $this->config_settings;
        if ($val = $this->get_nested_var($cs, $item)) {
          return $val;
        }
      }

      return $default_value;

  } 

  // --------------------------------------------------------------

  /**
   * Returns configuration items (or all items) as an array
   *
   * @param  string|array   Array of items or single item
   * @return array
   */
  public function get_items($items = NULL) {

    if ($items) {

      if ( ! is_array($items)) {
        $items = array($items);
      }

      $output = array();
      foreach($items as $item) {
        $output[$item] = $this->get_item($item);
      }

      return $output;
    }
    else {
      return $this->config_settings;
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
  private function merge_config_arrays($arr1, $arr2) {

    foreach($arr2 as $key => $value)
    {
      if(array_key_exists($key, $arr1) && is_array($value))
        $arr1[$key] = $this->merge_config_arrays($arr1[$key], $arr2[$key]);

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
  private function get_nested_var(&$context, $name) {

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