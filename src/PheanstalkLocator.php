<?php

namespace Leezy\PheanstalkBundle;

use Pheanstalk\PheanstalkInterface;

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
    public function getPheanstalks()
    {
        return $this->pheanstalks;
    }

    /**
     * @param string $name
     *
     * @return PheanstalkInterface
     */
    public function getPheanstalk($name = null)
    {
        $name = null !== $name ? $name : $this->default;

        if (array_key_exists($name, $this->pheanstalks)) {
            return $this->pheanstalks[$name];
        }

        return;
    }

    /**
     * @return array
     */
    public function getDefaultPheanstalk()
    {
        return $this->getPheanstalk();
    }

    /**
     * @param string              $name
     * @param PheanstalkInterface $pheanstalk
     * @param bool                $default
     */
    public function addPheanstalk($name, PheanstalkInterface $pheanstalk, $default = false)
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
