<?php

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