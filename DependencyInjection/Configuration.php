<?php

namespace C33s\CoreBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('c33s_core');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                    ->end()
                    ->append($this->addAppNode())
                    ->append($this->addGoogleNode())
                    ->append($this->addPageNode())
                    ->append($this->addContactNode())
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function addGoogleNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('google');

        $node
            ->children()
                ->arrayNode('tracking')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('id')->defaultNull()->end()
                        ->booleanNode('multi_domain')->defaultFalse()->end()
                        ->booleanNode('enable_GDN_impression_reporting')->defaultFalse()->end()
                        ->scalarNode('domain')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('webmaster_tools')
                    ->children()
                        ->scalarNode('verify')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $node;
    }

    public function addAppNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('app');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('debug')->defaultFalse()->end()
                ->booleanNode('hinclude')->defaultTrue()->end()
                ->booleanNode('use_cdn')->defaultFalse()->end()
                ->booleanNode('use_scrollspy')->defaultFalse()->end()
                ->scalarNode('version')->defaultNull()->end()
                ->scalarNode('name')->defaultNull()->end()
                ->scalarNode('name_short')->defaultNull()->end()
                ->arrayNode('production_company')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('consistency.at')->end()
                        ->scalarNode('link')->defaultValue('http://consistency.at')->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $node;
    }

    public function addPageNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('page');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('title')->defaultNull()->end()
                ->scalarNode('description')->defaultNull()->end()
            ->end()
            ->fixXmlConfig('author')
            ->beforeNormalization()
                ->ifString()
                ->then(function($v) { return array('authors'=> $v); })
            ->end()
            ->children()
                ->arrayNode('authors')
                    //->defaultValue(array('none'))
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->fixXmlConfig('keyword')
            ->children()
                ->arrayNode('keywords')
                    //->defaultValue(array('none'))
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            //->append($this->addKeywordsNode())
        ->end()
        ;

        return $node;
    }

    public function addKeywordsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('keywords');

        $node
            ->fixXmlConfig('keyword')
            ->children()
                ->prototype('scalar')
                ->end()
            ->end()
        ;

        return $node;
    }

    public function addContactNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('contact');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('company')->defaultNull()->end()
                ->scalarNode('street')->defaultNull()->end()
                ->scalarNode('mobile')->defaultNull()->end()
                ->scalarNode('phone')->defaultNull()->end()
                ->scalarNode('fax')->defaultNull()->end()
                ->scalarNode('mail')->defaultNull()->end()
                ->scalarNode('www')->defaultNull()->end()
                ->scalarNode('zip')->defaultNull()->end()
                ->scalarNode('city')->defaultNull()->end()
                ->scalarNode('country')->defaultNull()->end()
                ->scalarNode('title')->defaultNull()->end()
                ->scalarNode('firstname')->defaultNull()->end()
                ->scalarNode('middlename')->defaultNull()->end()
                ->scalarNode('lastname')->defaultNull()->end()
                ->scalarNode('title_post')->defaultNull()->end()
                ->scalarNode('company')->defaultNull()->end()
            ->end()
            ->append($this->addMapNode())
        ;

        return $node;
    }

    public function addMapNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('map');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->floatNode('lat')->defaultValue(0.0)->end()
                ->floatNode('long')->defaultValue(0.0)->end()
                ->booleanNode('scrollwheel')->defaultTrue()->end()
                ->arrayNode('marker')
                    ->children()
                        ->scalarNode('image')->defaultValue('http://maps.google.com/mapfiles/marker_orange.png')->end()
                        ->variableNode('size')->defaultValue(array(20,34))->end()
                        ->variableNode('point')->defaultValue(array(50,50))->end()
                        ->arrayNode('shadow')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('image')->defaultValue('http://maps.google.com/mapfiles/shadow50.png')->end()
                                ->variableNode('size')->defaultValue(array(40,34))->end()
                                ->variableNode('point')->defaultValue(array(50,50))->end()
                            ->end()
                        ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
