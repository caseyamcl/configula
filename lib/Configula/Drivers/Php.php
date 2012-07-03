<?php

namespace Configula\Drivers;

class Php implements \Configula\DriverInterface
{
	public function read($filepath)
	{
		ob_start();
		include($filepath);
		ob_end_clean();

		return (isset($config) && is_array($config)) ? $config : array();
	
	}
}

/* EOF: Php.php */