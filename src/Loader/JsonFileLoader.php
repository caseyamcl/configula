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
     * JsonFileLoader constructor.
     * @param string $filePath
     * @param bool $required
     * @throws \Exception
     */
    public function __construct(string $filePath, bool $required = false)
    {
        parent::__construct($filePath, $required);

        if (! is_callable('json_decode')) {
            throw new \Exception("Missing required extension: ext-json");
        }
    }


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
        elseif (! $decoded = @json_decode($rawFileContents, true)) {
            throw new ConfigLoaderException("Could not parse JSON file: ". $this->getFilePath());
        }

        return $decoded;
    }
}