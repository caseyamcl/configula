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
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ChainConfigSource implements ConfigSourceInterface
{
    /**
     * @var array|ConfigSourceInterface[]
     */
    protected $configSources;

    /**
     * Constructor
     *
     * @param array|ConfigSourceInterface[] $configSources
     */
    public function __construct(array $configSources)
    {
        foreach ($configSources as $source) {
            $this->addConfigSource($source);
        }
    }

    // ---------------------------------------------------------------

    /**
     * Add a configuration source to the list
     *
     * @param ConfigSourceInterface $source
     */
    public function addConfigSource(ConfigSourceInterface $source)
    {
        $this->configSources[] = $source;
    }

    // ---------------------------------------------------------------

    /**
     * Load configuration values from a source
     *
     * @param array $options Optional runtime values
     * @return array
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
