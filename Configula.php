<?php

namespace Configurator;

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
	public function __construct($defaults = array(), $config_path = NULL)
	{
    //Set the defaults
    $this->config_settings = $defaults;
    
    //Load the config files
    if ($config_path) {
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
		//Append trailing slash
		if (substr($config_path, strlen($config_path) - 1) != DIRECTORY_SEPARATOR)
			$config_path .= DIRECTORY_SEPARATOR;

		//Array to hold the configuration items as we get them
		$config = array();
		
		//Unparsed local files
		$unparsed_local_files = array();	
		
		//Path good?
		if ( ! is_readable($config_path))
			throw new \Exception("Cannot read from config path!  Does it exist?  Is it readable?");
		
		//Scan the directory for any files ending in .php (but not .local.php)
		foreach(scandir($config_path) as $filename)
		{
			if (substr($filename, 0, 1) == '.' OR substr($filename, strlen($filename)-strlen('.php')) !== '.php')
				continue;
			
			//Run all of the local files last
			if (substr($filename, strlen($filename) - strlen('.local.php')) == '.local.php')
				$unparsed_local_files[] = $filename;
			else
				include($config_path . $filename);
		}
		
		foreach($unparsed_local_files as $file)
			include($config_path . $file);
		
		$this->config_settings = $config;
		
		return count($this->config_settings);
	}
	
	// --------------------------------------------------------------
  
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
	public function get_item($item = NULL, $default_value = NULL)
	{
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
