<?php

namespace Configula\Filter;

use Configula\ConfigValues;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class SymfonyConfigFilterTest extends TestCase
{
    /**
     * Test processing a valid configuration works correctly
     */
    public function testProcessValidConfig(): void
    {
        $values = new ConfigValues(['config' => ['foo' => true, 'bar' => 'test']]);
        $newValues = (new SymfonyConfigFilter($this->getConfiguration()))->__invoke($values);
        $this->assertEquals(true, $newValues->get('foo'));
        $this->assertEquals('test', $newValues->get('bar'));
    }

    /**
     * Test processing invalid configuration throws expected exception
     */
    public function testProcessingInvalidConfigThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $values = new ConfigValues(['config' => ['foo' => 'xxx', 'bar' => 'test']]); // 'foo' should be boolean..
        (new SymfonyConfigFilter($this->getConfiguration()))->__invoke($values);
    }

    public function testStaticFilterMethod(): void
    {
        $values = new ConfigValues(['config' => ['foo' => true, 'bar' => 'test']]);
        $newValues = SymfonyConfigFilter::filter($this->getConfiguration(), $values);
        $this->assertEquals(true, $newValues->get('foo'));
        $this->assertEquals('test', $newValues->get('bar'));
    }

    /**
     * @return ConfigurationInterface
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new class implements ConfigurationInterface {
            /**
             * Generates the configuration tree builder.
             *
             * @return TreeBuilder The tree builder
             */
            public function getConfigTreeBuilder()
            {
                $builder = new TreeBuilder('config');
                $builder->getRootNode()
                    ->children()
                    ->booleanNode('foo')->isRequired()->end()
                    ->scalarNode('bar')->isRequired()->end()
                    ->end();

                return $builder;
            }
        };
    }
}
