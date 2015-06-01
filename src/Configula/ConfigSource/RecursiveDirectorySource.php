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

use IteratorIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Recursive Directory Source
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class RecursiveDirectorySource extends DirectorySource
{
    /**
     * Get file iterator
     *
     * @param string $path;
     * @return IteratorIterator|SplFileInfo[]
     */
    protected function getFileIterator($path)
    {
        new IteratorIterator(new RecursiveDirectoryIterator($path));
    }
}
