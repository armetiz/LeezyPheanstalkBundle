<?php

namespace Leezy\PheanstalkBundle\DependencyInjection;

use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;
use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LeezyPheanstalkExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('commands.xml');

        $this->configureConnections($container, $config);

        if ($config['profiler']['enabled']) {
            $this->configureProfiler($container, $config);
        }
    }

    /**
     * Configures the Connections and Connection Locator.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @throws PheanstalkException
     */
    public function configureConnections(ContainerBuilder $container, array $config)
    {
        // Create a connection locator that will reference all existing connection
        $connectionLocatorDef = new Definition(PheanstalkLocator::class);
        $container->setDefinition('leezy.pheanstalk.pheanstalk_locator', $connectionLocatorDef);
        $container->setParameter('leezy.pheanstalk.pheanstalks', $config['pheanstalks']);
    }

    /**
     * Configures the profiler data collector.
     *
     * @param ContainerBuilder $container Container
     * @param array            $config    Configuration
     */
    public function configureProfiler(ContainerBuilder $container, array $config)
    {
        // Setup the data collector service for Symfony profiler
        $dataCollectorDef = new Definition(PheanstalkDataCollector::class);
        $dataCollectorDef->setPublic(false);
        $dataCollectorDef->addTag('data_collector', ['id' => 'pheanstalk', 'template' => $config['profiler']['template']]);
        $dataCollectorDef->addArgument(new Reference('leezy.pheanstalk.pheanstalk_locator'));

        $container->setDefinition('leezy.pheanstalk.data_collector', $dataCollectorDef);
    }
}
