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
class YamlFileLoader extends AbstractFileLoader
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * YamlFileLoader constructor.
     *
     * @param string $yamlFilePath
     * @param Parser|null $yamlParser
     */
    public function __construct(string $yamlFilePath, Parser $yamlParser = null)
    {
        $this->parser = $yamlParser ?: new Parser();
        parent::__construct($yamlFilePath);
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
            return $this->parser->parse($rawFileContents, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
        } catch (ParseException $e) {
            throw new ConfigLoaderException("Could parse YAML file: " . $this->getFilePath(), $e->getCode(), $e);
        }
    }
}