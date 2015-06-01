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
 * PHP File Configuration Source
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PhpFileConfigSource implements ConfigSourceInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $configArrayNames;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $path
     * @param array  $configArrayNames
     */
    public function __construct($path, array $configArrayNames = ['config'])
    {
        $this->path             = $path;
        $this->configArrayNames = $configArrayNames;
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
        if ( ! is_readable($this->path)) {
            throw new ConfigLoadingException("Configuration file path non-existent or not readable: " . $this->path);
        }

        ob_start();
        include($this->path);
        ob_clean();

        $out = array();

        foreach ($this->configArrayNames as $arrName) {
            if (isset($$arrName)) {
                $out = RecursiveArrayMerger::merge($out, $$arrName);
            }
        }

        return $out;
    }
}
