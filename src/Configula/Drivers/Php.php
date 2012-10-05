<?php

/**
 * Configula PHP File Driver
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 */

namespace Configula\Drivers;
use Configula\DriverInterface;

class Php implements DriverInterface
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