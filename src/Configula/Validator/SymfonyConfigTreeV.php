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

namespace Configula\Validator;

use Configula\Exception\ConfigValidationException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Symfony Config Tree Validator
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConfigTreeValidator implements ConfigValidatorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configTree;

    /**
     * @var Processor
     */
    private $processor;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param ConfigurationInterface $configTree
     * @param Processor              $processor
     */
    public function __construct(ConfigurationInterface $configTree, Processor $processor = null)
    {
        $this->configTree = $configTree;
        $this->processor  = $processor ?: new Processor();
    }

    // ---------------------------------------------------------------

    /**
     * Validate Configuration using Symfony
     *
     * This just wraps the Symfony Configuration Exception in the expected config exception type
     *
     * @param array $values
     * @return array
     * @throws ConfigValidationException
     */
    function validate(array $values)
    {
        try {
            $this->processor->processConfiguration($this->configTree, array($values));
        }
        catch (InvalidConfigurationException $e) {
            throw new ConfigValidationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
