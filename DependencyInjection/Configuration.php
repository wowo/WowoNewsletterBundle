<?php

namespace Wowo\NewsletterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
        $rootNode = $treeBuilder->root('wowo_newsletter');

        $rootNode
            ->children()
                ->arrayNode('placeholders')
                    ->addDefaultsIfNotSet()
                    ->defaultValue(array('name' => 'getFullName', 'email' => 'getEmail'))
                    ->useAttributeAsKey('')
                    ->cannotBeEmpty()
                    ->canBeUnset()
                    ->validate()
                        ->ifTrue(function($v) {
                            if (!isset($v['email'])) {
                                throw new InvalidConfigurationException('No email mapping in newslettr placeholders '
                                   .'mappings. Please define one in wowo_newsletter -> placeholders -> email');
                            }
                            return $v;
                        })
                        ->then(function($v) {return $v;})
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('templates')
                    ->useAttributeAsKey('')
                    ->validate()
                        ->ifTrue(function($v) {return $v;})
                        ->then(function($v) {return $v;})
                    ->end()
                    ->prototype('scalar')->end()
                ;

        return $treeBuilder;
    }
}
