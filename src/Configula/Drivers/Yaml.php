<?php

namespace Configula\Drivers;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Yaml implements \Configula\DriverInterface
{
    public function __construct()
    {
        if ( ! class_exists("\Symfony\Component\Yaml\Parser")) {
            throw new \Exception("Missing symfony/yaml dependency.");
        }
    }

    // --------------------------------------------------------------

    public function read($filepath)
    {
        try {
            return SymfonyYaml::parse($filepath) ?: array();
        }
        catch (ParseException $e) {
            return array();
        }
    } 
}

/* EOF: Yaml.php */