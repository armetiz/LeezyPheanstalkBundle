<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Pheanstalk_PheanstalkInterface;

interface ProxyInterface extends Pheanstalk_PheanstalkInterface {
    /**
     * @return \Pheanstalk_PheanstalkInterface
     */
    public function getPheanstalk();
    
    /**
     * @param \Pheanstalk_PheanstalkInterface $pheanstalk
     */
    public function setPheanstalk(Pheanstalk_PheanstalkInterface $pheanstalk);
}
