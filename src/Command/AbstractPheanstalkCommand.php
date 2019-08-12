<?php

namespace Leezy\PheanstalkBundle\Command;

use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Contract\PheanstalkInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractPheanstalkCommand extends Command
{
    /** @var PheanstalkLocator */
    protected $locator;

    public function __construct(PheanstalkLocator $locator)
    {
        parent::__construct();

        $this->locator = $locator;
    }

    protected function getPheanstalk(string &$name = null): PheanstalkInterface
    {
        $pheanstalk = $this->locator->getPheanstalk($name);

        if (null === $name) {
            $name = 'default';
        }

        if (null === $pheanstalk) {
            throw new \RuntimeException('Pheanstalk not found: '.$name);
        }

        return $pheanstalk;
    }
}
