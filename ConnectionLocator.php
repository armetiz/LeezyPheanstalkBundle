<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ConnectionFinder
{
    private $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function getConnection ($name) {
        if ("default" == $name) {
            
            $serviceName = "leezy.pheanstalk";
        }
        else {
            $serviceName = "leezy.pheanstalk." . $name;
        }
        
        if (!$this->container->has($serviceName)) {
            return null;
        }
        
        return $this->container->get($serviceName);
    }
}
