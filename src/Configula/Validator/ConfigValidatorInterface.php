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

namespace Configula\Validator;


use Configula\Exception\ConfigValidationException;

interface ConfigValidatorInterface
{
    /**
     * Validate Configuration
     *
     * @param array $values
     * @return array
     * @throws ConfigValidationException
     */
    function validate(array $values);
}
