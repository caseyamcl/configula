<?php

/**
 * Configula INI File Driver
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 */

namespace Configula\Drivers;
use Configula\DriverInterface;

class Ini implements DriverInterface
{
    public function read($filepath)
    {
        return parse_ini_file($filepath, TRUE) ?: array();
    } 
}

/* EOF: Ini.php */