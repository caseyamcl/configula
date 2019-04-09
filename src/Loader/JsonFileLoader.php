<?php

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;

/**
 * Class JsonFileLoader
 * @package Configula\Loader
 */
class JsonFileLoader extends FileLoader
{
    /**
     * Parse the contents
     * @param string $rawFileContents
     * @return array
     */
    protected function parse(string $rawFileContents): array
    {
        if (trim($rawFileContents) === '') {
            return [];
        }
        elseif (! $decoded = @json_decode($rawFileContents)) {
            throw new ConfigLoaderException("Could not parse JSON file: ". $this->getFilePath());
        }

        return (array) $decoded;
    }
}