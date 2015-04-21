<?php

namespace Leezy\PheanstalkBundle\Command;

use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\PheanstalkInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractPheanstalkCommand extends Command
{
    /**
     * @var PheanstalkLocator
     */
    protected $locator;

    /**
     * @param PheanstalkLocator $locator
     */
    public function __construct(PheanstalkLocator $locator)
    {
        parent::__construct();

        $this->locator = $locator;
    }

    /**
     * @param string $name
     *
     * @return PheanstalkInterface
     */
    protected function getPheanstalk(&$name = null)
    {
        $pheanstalk = $this->locator->getPheanstalk($name);

        if (null === $name) {
            $name = 'default';
        }

        if (null === $pheanstalk) {
            throw new \RuntimeException('Pheanstalk not found: '.$name);
        }

        if (!$pheanstalk->getConnection()->isServiceListening()) {
            throw new \RuntimeException('Pheanstalk not connected: '.$name);
        }

        return $pheanstalk;
    }
}
