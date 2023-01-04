<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AppConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('app');

        $treeBuilder->getRootNode()
                ->children()
                    ->scalarNode('test')
                        ->defaultValue('default')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
