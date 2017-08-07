<?php

namespace Configula;
use Configula\Loader\ConfigFolderFilesLoader;
use Configula\Util\RecursiveArrayMerger;

/**
 * Config Facade Class
 *
 * Provides convenience methods (compatible with 2.x API)
 *
 * @package Configula
 */
class Config extends ConfigValues
{
    /**
     * Config constructor.
     *
     * @param string|null $configPath
     * @param array $defaults
     */
    public function __construct(string $configPath = null, array $defaults = [])
    {
        $pathValues = ($configPath)
            ? (new ConfigFolderFilesLoader($configPath))->load()->getArrayCopy()
            : [];

        $values = RecursiveArrayMerger::merge($defaults, $pathValues);
        parent::__construct($values);
    }
}