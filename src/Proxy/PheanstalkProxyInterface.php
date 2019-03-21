<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Pheanstalk\Contract\PheanstalkInterface;

interface PheanstalkProxyInterface extends PheanstalkInterface
{
    public function getPheanstalk(): PheanstalkInterface;

    public function setPheanstalk(PheanstalkInterface $pheanstalk): void;

    public function getName(): string;

    public function setName(string $name);
}
