<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Pheanstalk\Contract\PheanstalkInterface;

interface PheanstalkProxyInterface extends PheanstalkInterface
{
    public function getPheanstalk(): PheanstalkInterface;
}
