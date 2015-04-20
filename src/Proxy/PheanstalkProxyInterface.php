<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Pheanstalk\PheanstalkInterface;

interface PheanstalkProxyInterface extends PheanstalkInterface
{
    /**
     * @return PheanstalkInterface
     */
    public function getPheanstalk();

    /**
     * @param PheanstalkInterface $pheanstalk
     */
    public function setPheanstalk(PheanstalkInterface $pheanstalk);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);
}
