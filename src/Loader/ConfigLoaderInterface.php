<?php

namespace Configula\Loader;

use Configula\ConfigValues;

/**
 * Interface ConfigLoaderInterface
 * @package FandF\Config
 */
interface ConfigLoaderInterface
{
    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues;
}