<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

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
     * @throws ConfigLoaderException  If loading fails for whatever reason, throw this exception
     */
    public function load(): ConfigValues;
}