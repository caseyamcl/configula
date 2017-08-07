<?php

namespace Configula\Loader;
use Configula\ConfigValues;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Class PhpFileLoader
 * @package Configula\Loader
 */
class PhpFileLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * IniFileLoader constructor.
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        if (is_readable($this->filePath)) {
            ob_start();
            include($this->filePath);
            ob_end_clean();
        }

        if (isset($config) && is_array($config)) {
            return new ConfigValues($config);
        }
        else {
            throw new ParseException("Missing or invalid \$config array in file: " . $this->filePath);
        }
    }
}