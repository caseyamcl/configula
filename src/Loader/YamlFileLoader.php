<?php

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFileLoader
 *
 * @package FandF\Config
 */
class YamlFileLoader extends FileLoader
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * YamlFileLoader constructor.
     *
     * @param string $yamlFilePath
     * @param bool $required
     * @param Parser|null $yamlParser
     */
    public function __construct(string $yamlFilePath, bool $required = true, Parser $yamlParser = null)
    {
        $this->parser = $yamlParser ?: new Parser();
        parent::__construct($yamlFilePath, $required);
    }

    /**
     * Parse the contents
     *
     * @param string $rawFileContents
     * @return array
     */
    protected function parse(string $rawFileContents): array
    {
        try {
            return (array) $this->parser->parse($rawFileContents, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
        } catch (ParseException $e) {
            throw new ConfigLoaderException("Could not parse YAML file: " . $this->getFilePath(), $e->getCode(), $e);
        }
    }
}