<?php

namespace Configula\Drivers;

class Php implements \Configula\DriverInterface {

  public function read($filepath) {

    include($filepath);
    return (isset($config) && is_array($config)) ? $config : array();
    
  } 
}

/* EOF: Php.php */