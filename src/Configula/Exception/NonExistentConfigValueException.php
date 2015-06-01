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

namespace Configula\Exception;

/**
 * Config Value Exception
 *
 * Thrown when user attempts to access a configuration value that doesn't exist
 * and does not provide a default value at runtime
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class NonExistentConfigValueException extends ConfigulaException {

}
