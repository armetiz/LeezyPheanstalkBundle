<?php
namespace Leezy\PheanstalkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;

class ProxyCompilerPass implements CompilerPassInterface
{
    protected function reservedName()
    {
        return array(
                'pheanstalks',
                'pheanstalk_locator',
                'proxy',
                'data_collector',
                'listener',
                'event',
            );
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('leezy.pheanstalk.pheanstalks')) {
            return;
        }

        $defaultPheanstalkName = null;
        $pheanstalks = $container->getParameter('leezy.pheanstalk.pheanstalks');

        $pheanstalkLocatorDef = $container->getDefinition("leezy.pheanstalk.pheanstalk_locator");

        // For each connection in the configuration file
        foreach ($pheanstalks as $name => $pheanstalk) {
            if (in_array($name, $this->reservedName())) {
                throw new \RuntimeException('Reserved pheanstalk name : ' . $name);
            }

            $pheanstalkConfig = array($pheanstalk['server'], $pheanstalk['port'], $pheanstalk['timeout']);
            $isDefault = $pheanstalk['default'];

            $pheanstalkDef = $container->getDefinition($pheanstalk['proxy']);

            //TODO: Add Reflection to check PheanstalkProxyInterface implementation
            //$pheanstalkRefl = new \ReflectionClass($pheanstalkDef->getClass());
            //$pheanstalkRefl->implementsInterface('Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface')
            $pheanstalkDef->addMethodCall('setPheanstalk', array(new Definition('Pheanstalk_Pheanstalk', $pheanstalkConfig)));
            $pheanstalkDef->addMethodCall('setName', array($name));

            $container->setDefinition("leezy.pheanstalk." . $name, $pheanstalkDef);

            // Register the connection in the connection locator
            $pheanstalkLocatorDef->addMethodCall('addPheanstalk', array(
                $name,
                $container->getDefinition("leezy.pheanstalk." . $name),
                $isDefault
            ));

            if ($isDefault) {
                if (null !== $defaultPheanstalkName) {
                    throw new PheanstalkException(sprintf("Default pheanstalk already defined. '%s' & '%s'", $defaultPheanstalkName, $name));
                }

                $defaultPheanstalkName = $name;
                $container->setAlias("leezy.pheanstalk", "leezy.pheanstalk." . $name);
            }
        }
    }
}
