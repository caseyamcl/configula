<?php

namespace Configula\Loader;

use Configula\ConfigValues;

/**
 * Class ArrayValuesLoader
 *
 * @package Configula\Loader
 */
class ArrayValuesLoader implements ConfigLoaderInterface
{
    /**
     * @var array
     */
    private $configValues;

    /**
     * ArrayValuesLoader constructor.
     *
     * @param array $configValues
     */
    public function __construct(array $configValues)
    {
        $this->configValues = $configValues;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        return new ConfigValues($this->configValues);
    }
}
