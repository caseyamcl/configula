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
use Configula\Exception\ConfigLoadingException;

/**
 * Class JsonDeserializer
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class JsonDeserializer implements DeserializerInterface
{
    /**
     * Deserialize a string into an array of configuration values
     *
     * @param string $rawString
     * @param array  $options
     * @return array
     */
    public function deserialize($rawString, array $options = [])
    {
        if ($vals = json_decode($rawString, true)) {
            return $vals;
        }
        else {
            throw new ConfigLoadingException("Could not decode JSON");
        }
    }
}
