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
 * Configula PHP File Driver
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Php implements DriverInterface
{
    public function read($filePath)
    {
        ob_start();
        include $filePath;
        ob_end_clean();

        return (isset($config) && is_array($config)) ? $config : array();
    }
}

/* EOF: Php.php */
