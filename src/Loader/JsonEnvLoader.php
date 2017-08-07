<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigParseException;

/**
 * Class JsonEnvLoader
 *
 * @package Configula\Loader
 */
class JsonEnvLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $envValueName;

    /**
     * JsonEnvLoader constructor.
     * @param string $envValueName
     */
    public function __construct(string $envValueName)
    {
        $this->envValueName = $envValueName;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $rawContent = getenv($this->envValueName);

        if (! $decoded = @json_decode($rawContent, true)) {
            throw new ConfigParseException("Could not parse JSON from environment variable: ". $this->envValueName);
        }

        return new ConfigValues($decoded);
    }
}