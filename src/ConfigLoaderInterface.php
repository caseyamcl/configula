<?php

namespace Configula;

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
interface ConfigLoaderInterface
{
    const AUTODETECT = null;

    /**
     * @param mixed   $path Path to file(s), environment variables, etc
     * @param array   $options
     * @return Config
     */
    public function load($path, array $options = []);
}
