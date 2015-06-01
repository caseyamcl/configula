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

interface DeserializerInterface
{
    /**
     * Deserialize a string into an array of configuration values
     *
     * @param $rawString
     * @return array
     * @throws ConfigLoadingException
     */
    function deserialize($rawString, array $options = []);
}
