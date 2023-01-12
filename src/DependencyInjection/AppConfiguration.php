<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AppConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('app');

        $treeBuilder->getRootNode()
                ->children()
                    ->scalarNode('title')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('default_locale')
                        ->validate()
                            ->ifNotInArray(['en', 'de'])
                            ->thenInvalid('Invalid locale %s')
                        ->end()
                    ->end()
                    ->arrayNode('features')
                        ->children()
                            ->booleanNode('enable_registration')
                                ->defaultValue(true)
                            ->end()
                            ->booleanNode('enable_terms')
                                ->defaultValue(true)
                            ->end()
                            ->booleanNode('enable_cookie_consent')
                                ->defaultValue(true)
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('mailer')
                        ->children()
                            ->scalarNode('from_address')
                                ->defaultValue('noreply@cois.dev')
                            ->end()
                            ->scalarNode('from_sender')
                                ->defaultValue('Collaborative IVENA Statistics')
                            ->end()
                            ->scalarNode('admin_address')
                                ->defaultValue('admin@cois.dev')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
