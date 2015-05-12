<?php

namespace SocialStatsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SocialStatsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $this->setTwitterParams($processedConfig['twitter'], $container);
        $this->setFacebookParams($processedConfig['facebook'], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'social_stats';
    }

    private function setTwitterParams(array $config, ContainerBuilder $container)
    {
        foreach ($config as $key => $value) {
            $container->setParameter($this->getAlias() . '.twitter.' . $key, $config[$key]);
        }
    }

    private function setFacebookParams(array $config, ContainerBuilder $container)
    {
        foreach ($config as $key => $value) {
            $container->setParameter($this->getAlias() . '.facebook.' . $key, $config[$key]);
        }
    }
}
