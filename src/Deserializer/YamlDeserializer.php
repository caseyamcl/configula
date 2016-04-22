<?php
/**
 * configula
 *
 * @license ${LICENSE_LINK}
 * @link ${PROJECT_URL_LINK}
 * @version ${VERSION}
 * @package ${PACKAGE_NAME}
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Configula\Deserializer;

use Configula\DeserializerInterface;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Symfony\Component\Yaml\Exception\ParseException as SymfonyParseException;

use Configula\Exception\ConfigLoadingException;

/**
 * YAML Deserializer
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class YamlDeserializer implements DeserializerInterface
{
    public function __construct()
    {
        if (! class_exists('\Symfony\Component\Yaml\Parser')) {
            throw new \Exception("Missing symfony/yaml dependency.");
        }
    }

    /**
     * Deserialize a string into an array of configuration values
     *
     * @param $rawString
     * @return array
     * @throws ConfigLoadingException
     */
    public function deserialize($rawString, array $options = [])
    {
        try {
            return SymfonyYaml::parse($rawString);
        } catch (SymfonyParseException $e) {
            throw new ConfigLoadingException("Could not parse YAML", $e->getCode(), $e);
        }
    }
}
