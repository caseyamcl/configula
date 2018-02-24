<?php

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;

/**
 * Class JsonFileLoader
 * @package Configula\Loader
 */
class JsonFileLoader extends AbstractFileLoader
{
    /**
     * Parse the contents
     * @param string $rawFileContents
     * @return array
     */
    protected function parse(string $rawFileContents): array
    {
        if (! $decoded = @json_decode($rawFileContents, true)) {
            throw new ConfigLoaderException("Could not parse JSON file: ". $this->getFilePath());
        }

        return $decoded;
    }
}