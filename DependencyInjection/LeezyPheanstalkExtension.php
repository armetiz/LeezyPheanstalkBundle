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
        
        if (!$config["enabled"])
            return;

        $this->configureConnection($container, $config);
        $this->configureConnectionLocator($container, $config);
        $this->configureProfiler($container, $config);
    }

    /**
     * Configures the Connections
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     * @throws \Leezy\PheanstalkBundle\Exceptions\PheanstalkException
     */
    public function configureConnection(ContainerBuilder $container, array $config)
    {
        $defaultConnectionName = null;

        foreach ($config["connection"] as $name => $connection) {
            $args = array($connection["server"], $connection["port"], $connection["timeout"]);
            $isDefault = $connection["default"];

            $pheanstalkDef = new Definition("Pheanstalk_Pheanstalk", $args);
            $pheanstalkDef->addTag('pheanstalk_connection', array('name' => $name));
            $container->setDefinition("leezy.pheanstalk." . $name, $pheanstalkDef);

            if ($isDefault) {
                if (null !== $defaultConnectionName) {
                    throw new PheanstalkException(sprintf("Default connection already defined. '%s' & '%s'", $defaultConnectionName, $name));
                }

                $defaultConnectionName = $name;
                $container->setAlias("leezy.pheanstalk", "leezy.pheanstalk." . $name);
                $pheanstalkDef->clearTag('pheanstalk_connection');
                $pheanstalkDef->addTag('pheanstalk_connection', array('default' => true, 'name' => $name));
            }
        }
    }

    /**
     * Configures the Connection locator
     *
     * @param ContainerBuilder $container Container
     * @param array            $config    Configuration
     */
    public function configureConnectionLocator(ContainerBuilder $container, array $config)
    {
        $connectionLocatorDef = new Definition("Leezy\PheanstalkBundle\ConnectionLocator");
        $container->setDefinition("leezy.pheanstalk.connection_locator", $connectionLocatorDef);

        // Add each connection to this service
        foreach ($container->findTaggedServiceIds('pheanstalk_connection') as $service_id => $args) {
            $is_default = isset($args[0]['default']) && true === $args[0]['default'] ? true : false;
            $connectionLocatorDef->addMethodCall('addConnection', array($args[0]['name'], new Reference($service_id), $is_default));
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
        $dataCollectorDef->addTag('data_collector', array('id' => 'pheanstalk', 'template' => 'LeezyPheanstalkBundle:Profiler:pheanstalk'));
        $dataCollectorDef->addArgument(new Reference('leezy.pheanstalk.connection_locator'));
        $container->setDefinition("leezy.data_collector.pheanstalk", $dataCollectorDef);
    }
}
