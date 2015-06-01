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

use Configula\Exception\ConfigLoadingException;

/**
 * Chain Config Source
 *
 * Accepts multiple sources and uses the configuration values from the first
 * one that returns data
 *
 * TODO: Reverse these classes; MergeConfigSource should extend ChainConfigSource!
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ChainConfigSource extends MergeConfigSource
{
    /**
     * @param array|ConfigSourceInterface $configSources
     */
    public function __construct(array $configSources)
    {
        parent::__construct($configSources);
    }

    /**
     * Load configuration values from a source
     *
     * @param array $options Optional runtime values
     */
    public function getValues(array $options = [])
    {
        foreach ($this->configSources as $source) {

            try {
                $vals = $source->getValues($options);

                if ( ! empty($vals)) {
                    return $vals;
                }

            }
            catch (ConfigLoadingException $e) {
                // pass..
            }
        }

        return [];
    }
}
