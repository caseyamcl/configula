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

namespace Loader;

use Configula\Config;
use Configula\ConfigLoaderInterface;
use Configula\DeserializerInterface;

/**
 * File Loader
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class FileLoader implements ConfigLoaderInterface
{
    /**
     * @param string[]|string       $path          Single file path or iterator of paths
     * @param DeserializerInterface $deserializer
     * @param array                 $options
     * @return Config
     */
    public function load($path, DeserializerInterface $deserializer, array $options = [])
    {
        // Convert string to array so it can be iterated
        if (is_string($path) OR $path instanceof \SplFileInfo) {
            $path = [$path];
        }
        elseif ( ! is_array($path) OR ! $path instanceOf \Traversable) {
            throw new \InvalidArgumentException("FileLoader path must be a single filepath or iterator/array of filepaths (or SplFileInfo object(s))");
        }

        
    }
}
