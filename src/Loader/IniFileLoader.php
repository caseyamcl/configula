<?php

namespace Configula\Loader;

use Configula\ConfigValues;

/**
 * Class IniFileLoader
 * @package Configula\Loader
 */
class IniFileLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var bool
     */
    private $processSections;

    /**
     * IniFileLoader constructor.
     * @param string $filePath
     * @param bool $processSections
     */
    public function __construct(string $filePath, bool $processSections = true)
    {
        $this->filePath = $filePath;
        $this->processSections = $processSections;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        if (is_readable($this->filePath)) {
            $values = parse_ini_file($this->filePath, $this->processSections) ?: array();
        }

        return new ConfigValues($values ?? []);
    }
}