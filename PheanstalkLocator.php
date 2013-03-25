<?php

namespace Leezy\PheanstalkBundle;

use Pheanstalk_PheanstalkInterface;

class PheanstalkLocator
{
    private $pheanstalks;
    private $default;

    public function __construct()
    {
        $this->pheanstalks = array();
    }

    /**
     * @return array
     */
    public function getPheanstalks()
    {
        return $this->pheanstalks;
    }

    /**
     * @param string $name
     *
     * @return \Pheanstalk_PheanstalkInterface
     */
    public function getPheanstalk($name = null)
    {
        $name = null !== $name ? $name : $this->default;

        if (array_key_exists($name, $this->pheanstalks)) {
            return $this->pheanstalks[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getDefaultPheanstalk()
    {
        return $this->getPheanstalk();
    }

    /**
     * @param string                          $name
     * @param \Pheanstalk_PheanstalkInterface $pheanstalk
     * @param boolean                         $default
     */
    public function addPheanstalk($name, Pheanstalk_PheanstalkInterface $pheanstalk, $default = false)
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
