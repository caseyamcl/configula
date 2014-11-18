<?php

/**
 * Configula - A simple configuration tool
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * ------------------------------------------------------------------
 */

namespace Configula\Drivers;

use Configula\DriverInterface;

/**
 * INI Configula Driver
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Ini implements DriverInterface
{
    public function read($filePath)
    {
        return parse_ini_file($filePath, true) ?: array();
    }
}

/* EOF: Ini.php */
