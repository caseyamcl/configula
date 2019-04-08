<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * Json Env Loader
 *
 * Loads JSON tree from single environment variable
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
     * @var bool
     */
    private $asAssoc;

    /**
     * JsonEnvLoader constructor.
     * @param string $envValueName
     * @param bool $asAssoc
     */
    public function __construct(string $envValueName, bool $asAssoc = false)
    {
        $this->envValueName = $envValueName;
        $this->asAssoc = $asAssoc;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $rawContent = getenv($this->envValueName);

        if (! $decoded = @json_decode($rawContent, $this->asAssoc)) {
            throw new ConfigLoaderException("Could not parse JSON from environment variable: ". $this->envValueName);
        }

        return new ConfigValues((array) $decoded);
    }
}