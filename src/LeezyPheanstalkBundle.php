<?php

namespace Leezy\PheanstalkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Leezy\PheanstalkBundle\DependencyInjection\Compiler\ProxyCompilerPass;

class LeezyPheanstalkBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
 
        $container->addCompilerPass(new ProxyCompilerPass());
    }
}
