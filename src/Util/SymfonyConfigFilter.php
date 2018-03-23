<?php

namespace Configula\Util;

use Configula\ConfigValues;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class SymfonyConfigFilter
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
     * @param ConfigurationInterface $configuration
     * @param ConfigValues $values
     */
    public static function filter(ConfigurationInterface $configuration, ConfigValues &$values)
    {
        $that = new static($configuration);
        $that->process($values);
    }

    /**
     * SymfonyConfigFilter constructor.
     *
     * @param ConfigurationInterface $configTree
     * @param Processor|null $processor
     */
    public function __construct(ConfigurationInterface $configTree, Processor $processor = null)
    {
        $this->configTree = $configTree;
        $this->processor = $processor ?: new Processor();
    }

    /**
     * Process configuration through Symfony
     *
     * @param ConfigValues $values
     */
    public function process(ConfigValues &$values): void
    {
        $values = new ConfigValues($this->processor->processConfiguration($this->configTree, $values->getArrayCopy()));
    }
}