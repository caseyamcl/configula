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

use Configula\ConfigSource\ConfigSourceInterface;

/**
 * Configuration Loader
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Loader
{
    /**
     * Load configuration
     *
     * @param ConfigSourceInterface $configSource
     * @param array                 $options
     * @return Config
     */
    public function load(ConfigSourceInterface $configSource, array $options = [])
    {
        return new Config($configSource->getValues($options));
    }

}
