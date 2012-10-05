<?php

/**
 * Configula Driver Interface
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 */

namespace Configula;

interface DriverInterface
{
    /**
     * Read Interface - Reads a configuration file
     *
     * @param string $filepath The absolute path the configuration file
     * @return array
     */
    public function read($filepath);
}

/* EOF: DriverInterface.php */
