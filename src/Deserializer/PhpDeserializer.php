<?php
/**
 * Configula
 *
 * @license ${LICENSE_LINK}
 * @link    ${PROJECT_URL_LINK}
 * @version ${VERSION}
 * @package ${PACKAGE_NAME}
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace Deserializer;

use Configula\DeserializerInterface;
use Configula\Exception\ConfigDeserializerException;
use Configula\Exception\ConfigLoadingException;

/**
 * PHP Deserializer
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PhpDeserializer implements DeserializerInterface
{
    /**
     * Deserialize a string into an array of configuration values
     *
     * @param string $rawString
     * @param array  $options
     * @return array
     * @throws ConfigLoadingException  If could not deserialize
     */
    function deserialize($rawString, array $options = [])
    {
        $options = array_replace([
            'config_var_name' => 'config'
        ], $options);

        include('data://text/plain;base64,' . base64_encode($rawString));

        $varName = $options['config_var_name'];
        if ( ! isset($$varName)) {
            throw new ConfigDeserializerException("Missing expected configuration variable: " . $varName);
        }

        return $$varName;
    }
}
