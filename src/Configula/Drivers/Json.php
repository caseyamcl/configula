<?php

/**
 * Configula JSON File Driver
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 */

namespace Configula\Drivers;

use Configula\DriverInterface;

class Json implements DriverInterface
{
    public function read($filePath)
    {
        $result = json_decode(file_get_contents($filePath), true);

        return $result ?: array();
    }
}

/* EOF: Json.php */
