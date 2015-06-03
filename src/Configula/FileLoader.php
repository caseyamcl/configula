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

/**
 * Config File Loader
 *
 * A helper class to load configuration from files in a directory, mirroring
 * the general functionality of Configula 2.x
 *
 * This class merges all YML, JSON, PHP, and INI files into a configuration array.
 * Any files where the basename (before extension) equals "_local" are loaded last
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class FileLoader extends Loader
{
    /**
     * Constructor
     */
    public function __construct(/* Options, but not of them should be required */)
    {
        // load options
    }

    // ---------------------------------------------------------------

    /**
     * Load configuration from path
     *
     * @param ConfigSource\ConfigSourceInterface $configPath
     * @param array                              $defaults
     * @return Config
     */
    public function load($configPath, array $defaults = [])
    {
        // todo: this
    }
}
