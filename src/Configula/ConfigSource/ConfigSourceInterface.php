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

/**
 * Newable configuration sources
 *
 * @package Configula\Loader
 */
interface ConfigSourceInterface
{
    /**
     * Load configuration values from a source
     *
     * @param array $options  Optional runtime values
     * @return array
     */
    public function getValues(array $options = []);
}
