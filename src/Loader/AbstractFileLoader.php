<?php

namespace Configula\Loader;
use Configula\ConfigValues;

/**
 * Class AbstractFileLoader
 * @package Configula\Loader
 */
abstract class AbstractFileLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * AbstractFileLoader constructor.
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        if (is_readable($this->filePath)) {
            $values = $this->parse(file_get_contents($this->filePath));
        }

        return new ConfigValues($values ?? []);
    }

    /**
     * Parse the contents
     * @param string $rawFileContents
     * @return array
     */
    abstract protected function parse(string $rawFileContents): array;

    /**
     * @return string
     */
    protected function getFilePath(): string
    {
        return $this->filePath;
    }
}