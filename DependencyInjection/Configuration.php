<?php

namespace Javer\InfluxDB\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Javer\InfluxDB\Bundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('javer_influx_db');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('url')->defaultValue('%env(INFLUXDB_URL)%')->end()
                ->scalarNode('mapping_dir')->defaultValue('%kernel.project_dir%/src/Measurement')->end()
                ->booleanNode('logging')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('types')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(static function ($v) {
                                return ['class' => $v];
                            })
                        ->end()
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
