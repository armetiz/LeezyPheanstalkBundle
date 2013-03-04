<?php

namespace Leezy\PheanstalkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;

/**
 * This is the class that loads and manages your bundle configuration
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
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        if (!$config['enabled']) {
            return;
        }

        $this->configureConnections($container, $config);

        if ($config['profiler']['enabled']) {
            $this->configureProfiler($container, $config);
        }
    }

    /**
     * Configures the Connections and Connection Locator
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     * @throws \Leezy\PheanstalkBundle\Exceptions\PheanstalkException
     */
    public function configureConnections(ContainerBuilder $container, array $config)
    {
        $defaultConnectionName = null;

        // Create a connection locator that will reference all existing connection
        $connectionLocatorDef = new Definition("Leezy\PheanstalkBundle\ConnectionLocator");
        $container->setDefinition("leezy.pheanstalk.connection_locator", $connectionLocatorDef);

        // For each connection in the configuration file
        foreach ($config['connection'] as $name => $connection) {
            $connectionConfig = array($connection['server'], $connection['port'], $connection['timeout']);
            $isDefault = $connection['default'];

            // Create a service definition and register the service in the container
            $pheanstalkDef = new Definition("Pheanstalk_Pheanstalk", $connectionConfig);

            // We tag each connection in case another bundle need to access those services
            $pheanstalkDef->addTag('pheanstalk_connection', array('name' => $name));
            $container->setDefinition("leezy.pheanstalk." . $name, $pheanstalkDef);

            // Register the connection in the connection locator
            $connectionLocatorDef->addMethodCall('addConnection', array(
                $name,
                $container->getDefinition("leezy.pheanstalk." . $name),
                $isDefault
            ));

            if ($isDefault) {
                if (null !== $defaultConnectionName) {
                    throw new PheanstalkException(sprintf("Default connection already defined. '%s' & '%s'", $defaultConnectionName, $name));
                }

                $defaultConnectionName = $name;
                $container->setAlias("leezy.pheanstalk", "leezy.pheanstalk." . $name);
            }
        }
    }

    /**
     * Configures the profiler data collector
     *
     * @param ContainerBuilder $container Container
     * @param array            $config    Configuration
     */
    public function configureProfiler(ContainerBuilder $container, array $config)
    {
        // Setup the data collector service for Symfony profiler
        $dataCollectorDef = new Definition('Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector');
        $dataCollectorDef->setPublic(false);
        $dataCollectorDef->addTag('data_collector', array('id' => 'pheanstalk', 'template' => $config['profiler']['template']));
        $dataCollectorDef->addArgument(new Reference('leezy.pheanstalk.connection_locator'));
        $container->setDefinition("leezy.data_collector.pheanstalk", $dataCollectorDef);
    }
}
