<?php

namespace SocialStatsBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('social_stats');

        $rootNode
          ->append($this->createTwitterIntegrationNode())
          ->append($this->createFacebookIntegrationNode())
          //->append($this->createGoogleAnalyticsIntegrationNode())
        ;

        return $treeBuilder;
    }

    private function createTwitterIntegrationNode()
    {
        $builder = new TreeBuilder();

        $node = $builder->root('twitter');

        $node
            ->children()
                ->scalarNode('base_url')
                    ->defaultValue('https://api.twitter.com/1.1/')
                ->end()
                ->scalarNode('api_key')->isRequired()->end()
                ->scalarNode('api_secret')->isRequired()->end()
                ->scalarNode('access_token')->isRequired()->end()
                ->scalarNode('access_token_secret')->isRequired()->end()
                ->scalarNode('owner_id')->isRequired()->end()
            ->end();

        return $node;
    }

    private function createFacebookIntegrationNode()
    {
        $builder = new TreeBuilder();

        $node = $builder->root('facebook');

        $node
          ->children()
          ->scalarNode('app_id')->isRequired()->end()
          ->scalarNode('api_secret')->isRequired()->end()
          ->end();

        return $node;
    }

    private function createGoogleAnalyticsIntegrationNode()
    {
        $builder = new TreeBuilder();

        $node = $builder->root('google_analytics');

        $node
          ->children()
          ->scalarNode('api_key')->isRequired()->end()
          ->scalarNode('api_secret')->isRequired()->end()
          ->scalarNode('user_id')->isRequired()->end()
          ->end();

        return $node;
    }
}