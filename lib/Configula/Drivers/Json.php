<?php

namespace Configula\Drivers;

class Json implements \Configula\DriverInterface
{
    public function read($filepath)
    {
        $result = json_decode(file_get_contents($filepath), TRUE);
        return $result ?: array();
    } 
}

/* EOF: Json.php */