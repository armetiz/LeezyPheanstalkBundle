<?php

namespace Leezy\PheanstalkBundle;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;
use Pheanstalk\Contract\PheanstalkInterface;

class PheanstalkLocator
{
    /** @var PheanstalkInterface[] */
    private $pheanstalks;

    /** @var string */
    private $default;

    public function __construct()
    {
        $this->pheanstalks = [];
    }

    /**
     * @return PheanstalkInterface[]
     */
    public function getPheanstalks(): array
    {
        return $this->pheanstalks;
    }

    public function getPheanstalk(string $name = null): ?PheanstalkInterface
    {
        $name = $name ?? $this->default;

        if (array_key_exists($name, $this->pheanstalks)) {
            return $this->pheanstalks[$name];
        }

        return null;
    }

    public function getPheanstalkName(PheanstalkInterface $pheanstalk): ?string
    {
        $name = array_search($pheanstalk, $this->pheanstalks, true);

        if(false === $name) {
            return null;
        }

        return $name;
    }

    public function getDefaultPheanstalk(): ?PheanstalkInterface
    {
        return $this->getPheanstalk();
    }

    public function addPheanstalk(string $name, PheanstalkInterface $pheanstalk, bool $default = false): void
    {
        $this->pheanstalks[$name] = $pheanstalk;

        // Set the default connection name
        if ($default) {
            $this->default = $name;
        }
    }
}
