<?php

namespace Configula\Loader;

use Configula\ConfigValues;

/**
 * Class CascadingLoader
 *
 * @package FandF\Config
 */
class CascadingConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var array|ConfigLoaderInterface[]
     */
    private $loaders;

    /**
     * CascadingLoader constructor.
     *
     * @param array|ConfigLoaderInterface[] $loaders  Loaders, in the order that you want to load them
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $config = new ConfigValues([]);

        foreach ($this->loaders as $loader) {
            $config = $config->merge($loader->load());
        }

        return $config;
    }
}