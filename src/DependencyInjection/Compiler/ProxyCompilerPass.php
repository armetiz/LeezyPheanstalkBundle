<?php

/*
 * (c) 2013 Wozbe
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leezy\PheanstalkBundle\DependencyInjection\Compiler;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Description of ProxyCompilerPass.
 */
class ProxyCompilerPass implements CompilerPassInterface
{
    protected function reservedName()
    {
        return [
            'pheanstalks',
            'pheanstalk_locator',
            'proxy',
            'data_collector',
            'listener',
            'event',
        ];
    }

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('leezy.pheanstalk.pheanstalks')) {
            return;
        }

        $defaultPheanstalkName = null;
        $pheanstalks           = $container->getParameter('leezy.pheanstalk.pheanstalks');

        $pheanstalkLocatorDef = $container->getDefinition('leezy.pheanstalk.pheanstalk_locator');

        // For each connection in the configuration file
        foreach ($pheanstalks as $name => $pheanstalk) {
            if (in_array($name, $this->reservedName(), true)) {
                throw new \RuntimeException('Reserved pheanstalk name: '.$name);
            }

            $isDefault        = $pheanstalk['default'];

            # @see https://github.com/armetiz/LeezyPheanstalkBundle/issues/61
            $pheanstalkDef = clone $container->getDefinition($pheanstalk['proxy']);

            $pheanstalkDef->setPublic(true);
            $pheanstalkDef->setArguments([
                (new Definition(Pheanstalk::class))
                    ->addArgument($pheanstalk['server'])
                    ->addArgument($pheanstalk['port'])
                    ->addArgument($pheanstalk['timeout'])
                    ->setFactory([Pheanstalk::class, 'create']),
                $name
            ]);

            $container->setDefinition('leezy.pheanstalk.'.$name, $pheanstalkDef);

            // Register the connection in the connection locator
            $pheanstalkLocatorDef->addMethodCall('addPheanstalk', [
                $name,
                $container->getDefinition('leezy.pheanstalk.'.$name),
                $isDefault,
            ]);

            if ($isDefault) {
                if (null !== $defaultPheanstalkName) {
                    throw new PheanstalkException(sprintf('Default pheanstalk already defined. "%s" & "%s"', $defaultPheanstalkName, $name));
                }

                $defaultPheanstalkName = $name;
                $legacyAlias = $container->setAlias('leezy.pheanstalk', 'leezy.pheanstalk.'.$name);
                if ($legacyAlias) {
                    $legacyAlias->setPublic(true);
                }

                $autoWiringAlias = $container->setAlias(PheanstalkInterface::class, 'leezy.pheanstalk');
                if ($autoWiringAlias) {
                    $autoWiringAlias->setPublic(true);
                }
            }
        }
    }
}
