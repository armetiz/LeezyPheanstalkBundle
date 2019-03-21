<?php

namespace Leezy\PheanstalkBundle;

use Pheanstalk\Contract\PheanstalkInterface;

class PheanstalkLocator
{
    /**
     * @var PheanstalkInterface[]
     */
    private $pheanstalks;

    /**
     * @var string
     */
    private $default;

    /**
     * Constructor.
     */
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

    public function getPheanstalk(string $name = null): PheanstalkInterface
    {
        $name = $name ?? $this->default;

        if (array_key_exists($name, $this->pheanstalks)) {
            return $this->pheanstalks[$name];
        }

        return null;
    }

    public function getDefaultPheanstalk(): PheanstalkInterface
    {
        return $this->getPheanstalk();
    }

    public function addPheanstalk(string $name, PheanstalkInterface $pheanstalk, bool $default = false): void
    {
        if (!is_bool($default)) {
            throw new \InvalidArgumentException('Default parameter have to be a boolean');
        }

        $this->pheanstalks[$name] = $pheanstalk;

        // Set the default connection name
        if ($default) {
            $this->default = $name;
        }
    }
}
