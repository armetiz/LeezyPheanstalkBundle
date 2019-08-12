<?php

namespace Leezy\PheanstalkBundle\DependencyInjection;

use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;
use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;
use Leezy\PheanstalkBundle\Listener\PheanstalkLogListener;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

        $this->configureLocator($container, $config);
        $this->configureLogListener($container, $config);
        $this->configureProfiler($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureLogListener(ContainerBuilder $container, array $config): void
    {
        if(false === $container->has('logger')) {
            return;
        }

        // Create a connection locator that will reference all existing connection
        $definition = new Definition(PheanstalkLogListener::class);
        $definition->addArgument(new Reference(PheanstalkLocator::class));
        $definition->addTag('kernel.event_subscriber');
        $definition->addTag('monolog.logger', [
            'channel' => 'pheanstalk',
        ]);

        $definition->addMethodCall('setLogger', [
            new Reference('logger')
        ]);

        $container->setDefinition('leezy.pheanstalk.listener.log', $definition)->setPublic(true);
        $container->setAlias(PheanstalkLogListener::class, 'leezy.pheanstalk.listener.log');
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureLocator(ContainerBuilder $container, array $config): void
    {
        // Create a connection locator that will reference all existing connection
        $connectionLocatorDef = new Definition(PheanstalkLocator::class);
        $container->setDefinition('leezy.pheanstalk.pheanstalk_locator', $connectionLocatorDef);
        $container->setAlias(PheanstalkLocator::class, 'leezy.pheanstalk.pheanstalk_locator');
        $container->setParameter('leezy.pheanstalk.pheanstalks', $config['pheanstalks']);
    }

    /**
     * Configures the profiler data collector.
     *
     * @param ContainerBuilder $container Container
     * @param array            $config    Configuration
     */
    private function configureProfiler(ContainerBuilder $container, array $config): void
    {
        if(false === $config['profiler']['enabled']) {
            return;
        }

        // Setup the data collector service for Symfony profiler
        $dataCollectorDef = new Definition(PheanstalkDataCollector::class);
        $dataCollectorDef->setPublic(false);
        $dataCollectorDef->addTag('data_collector', ['id' => 'pheanstalk', 'template' => $config['profiler']['template']]);
        $dataCollectorDef->addArgument(new Reference('leezy.pheanstalk.pheanstalk_locator'));

        $container->setDefinition('leezy.pheanstalk.data_collector', $dataCollectorDef);
        $container->setAlias(PheanstalkDataCollector::class, 'leezy.pheanstalk.data_collector');
    }
}
