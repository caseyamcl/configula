<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 4
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

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
final class YamlFileLoader extends AbstractFileLoader
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * YamlFileLoader constructor.
     *
     * @param string      $yamlFilePath
     * @param bool        $required
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
     * @param  string $rawFileContents
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
