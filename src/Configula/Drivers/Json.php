<?php

namespace Configula\Drivers;
use Configula\DriverInterface;

class Json implements DriverInterface
{
    public function read($filepath)
    {
        $result = json_decode(file_get_contents($filepath), TRUE);
        return $result ?: array();
    } 
}

/* EOF: Json.php */