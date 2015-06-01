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

/**
 * Array configuration source -- Stupidly simple source
 *
 * Useful for providing hard-coded default values or for unit testing
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ArrayConfigSource implements ConfigSourceInterface
{
    /**
     * @var array
     */
    private $values;

    // ---------------------------------------------------------------

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
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
        return $this->values;
    }
}
