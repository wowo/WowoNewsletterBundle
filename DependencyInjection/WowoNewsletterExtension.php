<?php

namespace Wowo\Bundle\NewsletterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WowoNewsletterExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $container->setParameter($this->getAlias() . '.placeholders.mapping', $config['placeholders']);
        if (isset($config['contact_class'])) {
            $container->setParameter($this->getAlias() . '.model.contact.class', $config['contact_class']);
        }
        if (isset($config['pheanstalk_address'])) {
            $container->setParameter($this->getAlias() . '.pheanstalk.address', $config['pheanstalk_address']);
        }
        if (isset($config['default_sender_name'])) {
            $container->setParameter($this->getAlias() . '.default.sender_name', $config['default_sender_name']);
        }
        if (isset($config['default_sender_email'])) {
            $container->setParameter($this->getAlias() . '.default.sender_email', $config['default_sender_email']);
        }
    }

}
