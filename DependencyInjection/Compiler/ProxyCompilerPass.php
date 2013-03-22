<?php
namespace Leezy\PheanstalkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;

class ProxyCompilerPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('leezy.pheanstalk.connections')) {
            return;
        }
        
        $defaultConnectionName = null;
        $connections = $container->getParameter('leezy.pheanstalk.connections');
        
        $connectionLocatorDef = $container->getDefinition("leezy.pheanstalk.connection_locator");
        
        // For each connection in the configuration file
        foreach ($connections as $name => $connection) {
            $connectionConfig = array($connection['server'], $connection['port'], $connection['timeout']);
            $isDefault = $connection['default'];
            
            //TODO : Add Reflection to check PheanstalkProxyInterface implementation
            
            $pheanstalkProxyDef = $container->getDefinition($connection['proxy']);
            $pheanstalkProxyDef->addMethodCall('setPheanstalk', array(new Definition('Pheanstalk_Pheanstalk', $connectionConfig))); 
            //$pheanstalkDef->addTag('pheanstalk_connection', array('name' => $name));
            
            $container->setDefinition("leezy.pheanstalk." . $name, $pheanstalkProxyDef);

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
}
