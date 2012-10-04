<?php

namespace Configula\Drivers;

class Ini implements \Configula\DriverInterface
{
    public function read($filepath)
    {
        return parse_ini_file($filepath, TRUE) ?: array();
    } 
}

/* EOF: Ini.php */