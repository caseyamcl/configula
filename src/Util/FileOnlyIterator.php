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

namespace Configula\Util;

use Iterator;
use RuntimeException;
use SplFileInfo;

/**
 * File Only Iterator
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class FileOnlyIterator extends \FilterIterator
{
    /**
     * @var array
     */
    private $allowedExts;

    /**
     * @var string
     */
    private $ignorePattern;

    /**
     * @param Iterator $directoryIterator
     * @param array    $allowedExts        Case-insensitive list of allowed file extensions (empty for all)
     * @param string   $ignorePattern      Ignore files where basename EXCLUDING extension matches specified regex (empty for any filename)
     */
    public function __construct(Iterator $directoryIterator, $allowedExts = [], $ignorePattern = '')
    {
        parent::__construct($directoryIterator);
        $this->allowedExts   = array_map('strtolower', array_values($allowedExts));
        $this->ignorePattern = $ignorePattern;
    }

    // ---------------------------------------------------------------

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     *
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        /** @var SplFileInfo $fileInfo */
        $fileInfo = $this->getInnerIterator()->current();

        // Non-files are bad
        if ( ! $fileInfo instanceOf SplFileInfo) {
            throw new RuntimeException("FileOnlyIterator only allows iterating over instances of SplFileInfo");
        }

        return ($fileInfo->isFile() && $this->isAllowedExt($fileInfo) && $this->isAllowedBasename($fileInfo));
    }

    // ---------------------------------------------------------------

    /**
     * Is allowed extension?
     *
     * @param SplFileInfo $file
     * @return bool
     */
    private function isAllowedExt(SplFileInfo $file)
    {
        return ( ! empty($this->allowedExts))
            ? (in_array(strtolower($file->getExtension()), $this->allowedExts))
            : true;
    }

    // ---------------------------------------------------------------

    /**
     * Is allowed basename (ignoring extension)
     *
     * @param SplFileInfo $file
     * @return bool
     */
    private function isAllowedBasename(SplFileInfo $file)
    {
        $bn = $file->getBasename($file->getExtension() ? '.' . $file->getExtension() : null);

        return ( ! empty($this->ignorePattern))
            ? ( ! preg_match($this->ignorePattern, $bn))
            : true;
    }
}
