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

use Configula\Exception\ConfigLoadingException;

/**
 * INI Deserializer
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class IniDeserializer implements DeserializerInterface
{
    /**
     * Deserialize a string into an array of configuration values
     *
     * @param string $rawString
     * @param array  $options
     * @return array
     */
    function deserialize($rawString, array $options = [])
    {
        $options = array_replace(array(
            'parse_sections' => true
        ), $options);

        if ($vals = parse_ini_string($rawString, $options['parse_sections'])) {
            return $vals;
        }
        else {
            throw new ConfigLoadingException('Could not parse INI values');
        }
    }
}
