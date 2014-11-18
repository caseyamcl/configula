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
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Symfony\Component\Yaml\Exception\ParseException as SymfonyParseException;

/**
 * YAML Configula Driver
 *
 * @package Configula\Drivers
 */
class Yaml implements DriverInterface
{
    public function __construct()
    {
        if ( ! class_exists('\Symfony\Component\Yaml\Parser')) {
            throw new \Exception("Missing symfony/yaml dependency.");
        }
    }

    // --------------------------------------------------------------

    public function read($filePath)
    {
        try {
            return SymfonyYaml::parse($filePath) ?: array();
        }
        catch (SymfonyParseException $e) {
            return array();
        }
    } 
}

/* EOF: Yaml.php */