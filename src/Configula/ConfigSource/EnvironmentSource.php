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
use Configula\Exception\ConfigLoadingException;

/**
 * Environment Source
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class EnvironmentSource implements ConfigSourceInterface
{
    /**
     * @var DeserializerInterface
     */
    private $deserializer;

    /**
     * @var string
     */
    private $variableName;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $variableName
     * @param DeserializerInterface $deserializer
     */
    public function __construct($variableName, DeserializerInterface $deserializer)
    {
        $this->variableName = $variableName;
        $this->deserializer = $deserializer;
    }

    // ---------------------------------------------------------------

    /**
     * Load configuration values from an environment variable
     *
     * @param array $options Optional runtime values
     * @return array
     */
    public function getValues(array $options = [])
    {
        if ( ! $rawVal = getenv($this->variableName)) {
            throw new ConfigLoadingException("Environment variable does not exist: " . $this->variableName);
        }

        return $this->deserializer->deserialize($rawVal);
    }
}
