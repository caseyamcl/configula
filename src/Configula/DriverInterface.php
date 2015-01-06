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

/**
 * Configula Driver Interface
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @package Configula
 */
interface DriverInterface
{
    /**
     * Read Interface - Reads a configuration file
     *
     * @param  string $filePath The absolute path the configuration file
     * @return array
     */
    public function read($filePath);
}

/* EOF: DriverInterface.php */
