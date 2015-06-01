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


use Configula\Deserializer\DeserializerInterface;
use Configula\Util\FileOnlyIterator;
use Configula\Util\RecursiveArrayMerger;
use DirectoryIterator;
use SplFileInfo;

/**
 * Directory Source
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class DirectorySource implements ConfigSourceInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array|DeserializerInterface[]
     */
    private $extensionMappings;

    /**
     * @var string  Empty for none
     */
    private $ignorePattern;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string                         $path
     * @param array|DeserializerInterface[]  $extensionMapping  A case-insensitive key/value list of extensions and their associated deserializers
     * @param string                         $ignorePattern     Optionally specify a regex pattern from the basename EXCLUDING extension to be ignored ('e.g. "_local$")
     */
    public function __construct($path, array $extensionMapping, $ignorePattern = '')
    {
        $this->path = $path;
        foreach ($extensionMapping as $ext => $deserializer) {
            $this->addExtensionMapping($ext, $deserializer);
        }

        $this->ignorePattern = $ignorePattern;
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
        $vals = array();

        $iterator = new FileOnlyIterator(
            $this->getFileIterator($this->path),
            array_keys($this->extensionMappings),
            $this->ignorePattern
        );

        foreach ($iterator as $file) {
            $deserializer = $this->extensionMappings[strtolower($file->getExtension())];

            RecursiveArrayMerger::merge(
                $vals,
                $deserializer->deserialize(file_get_contents($file), $options)
            );
        }

        return $vals;
    }

    // ---------------------------------------------------------------

    /**
     * Get file iterator
     *
     * @param string $path
     * @return DirectoryIterator|SplFileInfo[]
     */
    protected function getFileIterator($path)
    {
        new DirectoryIterator($path);
    }

    // ---------------------------------------------------------------

    /**
     * Add extension mapping
     *
     * @param string                $ext
     * @param DeserializerInterface $deserializer
     */
    final private function addExtensionMapping($ext, DeserializerInterface $deserializer)
    {
        $this->extensionMappings[strtolower($ext)] = $deserializer;
    }
}
