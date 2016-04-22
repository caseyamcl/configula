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

namespace Configula;

use Configula\Exception\ConfigLoadingException;

/**
 * Deserializer Interface
 *
 * @package Configula\Deserializer
 */
interface DeserializerInterface
{
    /**
     * Get file extensions that this deserializer should be associated with
     *
     * @return array|string[]  File extensions (without the dot ".")
     */
    function getFileExtensions();

    /**
     * Deserialize a string into an array of configuration values
     *
     * @param string $rawString
     * @param array  $options
     * @return array
     * @throws ConfigLoadingException  If could not deserialize
     */
    function deserialize($rawString, array $options = []);
}
