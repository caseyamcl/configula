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
   * @param $defaults
   * An optional list of defaults to fall back on, set at instantiation
   * 
   * @param $config_path
   * An optional absolute path to the configuration folder
   * 
   */
  public function __construct($defaults = array(), $config_path = NULL) {

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
    	$config = array_merge($config, $this->parse_config_file($config_path . $filename));
    }
    
    //Go back a second time and run all of the .local files
    foreach($unparsed_local_files as $filename) {
			$config = array_merge($config, $this->parse_config_file($config_path . $filename));
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
  		throw new Exception("Cannot read from the config file: $filepath");
  	}

		$parser_classname = 'Drivers\\' . ucfirst(strtolower(pathinfo($filepath, PATHINFO_EXTENSION)));

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
   * If no configuration item returned, this method returns an array
   * with all configuration items
   * 
   * @param string $item 
   * The configuration item to retrieve - Leave blank for all
   * 
   * @param mixed $default_value
   * The default value to return for a configuration item if no configuration item exists
   * 
   * @return mixed
   * An array containing all configuration items, or a specific configuration item
   * NULL if a specified configuration item does not exist
   */
  public function get_item($item = NULL, $default_value = NULL) {
    if ($item)
    {
      if (isset($this->config_settings[$item]))
        return $this->config_settings[$item];
      else
        return $default_value;
    }
    else
      return $this->config_settings;
  } 
}

/* EOF: config.php */