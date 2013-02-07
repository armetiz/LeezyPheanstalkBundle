<?php

namespace Leezy\PheanstalkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

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
        
        $defaultConnectionName = null;
        
        foreach ($config["connection"] as $name => $connection) {
            $server = $connection["server"];
            $port = $connection["port"];
            $timeout = $connection["timeout"];
            $isDefault = $connection["default"];
            
            $pheanstalkDef = new Definition("Pheanstalk_Pheanstalk", array ($server, $port, $timeout));
            $container->setDefinition("leezy.pheanstalk." . $name, $pheanstalkDef);
            
            if ($isDefault) {
                if (null !== $defaultConnectionName) {
                    throw new PheanstalkException(printf("Default connection already defined. '%s' & '%s'", $defaultConnectionName, $name));
                }
                
                $defaultConnectionName = $name;
                $container->setAlias("leezy.pheanstalk", "leezy.pheanstalk." . $name);
            }
        }
    }
}
