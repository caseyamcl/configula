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
readonly class SymfonyConfigFilter
{
    /**
     * Filter method allows single-call static usage of this class using default Processor
     */
    public static function filter(ConfigurationInterface $configuration, ConfigValues $values): ConfigValues
    {
        $that = new static($configuration);
        return $that($values);
    }

    final public function __construct(
        private ConfigurationInterface $configTree,
        private ?Processor $processor = null
    ) {
    }

    /**
     * Process configuration through Symfony
     *
     * @param  ConfigValues $values
     * @return ConfigValues
     */
    public function __invoke(ConfigValues $values): ConfigValues
    {
        $processor = $this->processor ?: new Processor();
        return new ConfigValues($processor->processConfiguration($this->configTree, $values->getArrayCopy()));
    }
}
