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
use Configula\Util\RecursiveArrayMerger;

/**
 * Merge Config Source
 *
 * Accepts multiple sources and merges config values together in the order the config
 * loaders are specified
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MergeConfigSource implements ConfigSourceInterface
{
    /**
     * @var array|ConfigSourceInterface[]
     */
    protected $configSources;

    /**
     * @var bool
     */
    private $ignoreErrors;

    // ---------------------------------------------------------------

    /**
     * @param array|ConfigSourceInterface $configSources
     * @param bool $ignoreErrors
     */
    public function __construct(array $configSources, $ignoreErrors = true)
    {
        $this->ignoreErrors = $ignoreErrors;
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
        $arr = array();

        foreach ($this->configSources as $source) {

            try {
                $vals = $source->getValues($options);
                $arr = RecursiveArrayMerger::merge($arr, $vals);
            }
            catch (ConfigLoadingException $e) {
                if ( ! $this->ignoreErrors) {
                    throw $e;
                }
            }
        }

        return $arr;
    }
}
