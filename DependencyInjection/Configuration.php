<?php

namespace Leezy\PheanstalkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('leezy_pheanstalk');

        $rootNode
            ->children()
                ->booleanNode("enabled")->defaultTrue()->end()
                ->arrayNode("connection")
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode("server")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("port")
                                ->cannotBeEmpty()
                                ->defaultValue("11300")
                            ->end()
                            ->scalarNode("timeout")
                                ->cannotBeEmpty()
                                ->defaultValue("60")
                            ->end()
                            ->booleanNode("default")
                                ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
