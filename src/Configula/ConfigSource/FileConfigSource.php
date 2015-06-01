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

namespace Configula\ConfigSource;


use Configula\Deserializer\DeserializerInterface;
use Configula\Exception\ConfigDeserializerException;
use Configula\Exception\ConfigLoadingException;

/**
 * Represents a file configuration source
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class FileConfigSource implements ConfigSourceInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var DeserializerInterface
     */
    private $deserializer;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string                $path
     * @param DeserializerInterface $deserializer
     */
    public function __construct($path, DeserializerInterface $deserializer)
    {
        $this->path         = $path;
        $this->deserializer = $deserializer;
    }

    // ---------------------------------------------------------------

    /**
     * Get configuration values
     *
     * @param array $options
     * @return array
     */
    public function getValues(array $options = [])
    {
        if ( ! is_readable($this->path)) {
            throw new ConfigLoadingException("Configuration file path non-existent or not readable: " . $this->path);
        }
        if ( ! $contents = file_get_contents($this->path)) {
            throw new ConfigLoadingException("Could not read configuration file from path: " . $this->path);
        }

        try {
            return $this->deserializer->deserialize($contents);
        }
        catch (ConfigDeserializerException $e) {
            throw new ConfigLoadingException(
                sprintf("Could not deserialize config at path (%s): %s ", $e->getMessage(), $this->path),
                $e->getCode(),
                $e
            );
        }
    }
}
