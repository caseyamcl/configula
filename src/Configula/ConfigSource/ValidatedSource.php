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


use Configula\Validator\ConfigValidatorInterface;

class ValidatedSource implements ConfigSourceInterface
{
    /**
     * @var ConfigSourceInterface
     */
    private $source;

    /**
     * @var ConfigValidatorInterface
     */
    private $validator;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param ConfigSourceInterface    $source
     * @param ConfigValidatorInterface $validator
     */
    public function __construct(ConfigSourceInterface $source, ConfigValidatorInterface $validator)
    {
        $this->source    = $source;
        $this->validator = $validator;
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
        $vals = $this->source->getValues($options);
        $this->validator->validate($vals);
        return $vals;
    }
}
