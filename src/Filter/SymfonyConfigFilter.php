<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 4
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Configula\Filter;

use Configula\ConfigValues;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class SymfonyConfigFilter
 *
 * @package Configula\Util
 */
class SymfonyConfigFilter
{
    /**
     * @var ConfigurationInterface
     */
    private $configTree;

    /**
     * @var null|Processor
     */
    private $processor;

    /**
     * Filter method allows single-call static usage of this class
     *
     * @param  ConfigurationInterface $configuration
     * @param  ConfigValues           $values
     * @return ConfigValues
     */
    public static function filter(ConfigurationInterface $configuration, ConfigValues $values): ConfigValues
    {
        $that = new static($configuration);
        return $that($values);
    }

    /**
     * SymfonyConfigFilter constructor.
     *
     * @param ConfigurationInterface $configTree
     * @param Processor|null         $processor
     */
    final public function __construct(ConfigurationInterface $configTree, Processor $processor = null)
    {
        $this->configTree = $configTree;
        $this->processor = $processor ?: new Processor();
    }

    /**
     * Process configuration through Symfony
     *
     * @param  ConfigValues $values
     * @return ConfigValues
     */
    public function __invoke(ConfigValues $values): ConfigValues
    {
        return new ConfigValues($this->processor->processConfiguration($this->configTree, $values->getArrayCopy()));
    }
}
