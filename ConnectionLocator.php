<?php

namespace Leezy\PheanstalkBundle;

use Pheanstalk_Pheanstalk;

class ConnectionLocator
{
    private $connections;
    private $default;
    
    public function __construct()
    {
        $this->connections = array();
    }
    
    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }
    
    /**
     * @param string $name
     * 
     * @return \Pheanstalk_Connection $connection
     */
    public function getConnection($name = null) {
        $name = null !== $name ? $name : $this->default;

        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getDefaultConnection()
    {
        return $this->getConnection();
    }
    
    /**
     * @param string $name
     * @param Pheanstalk_Pheanstalk $connection
     * @param array $info
     */
    public function addConnection($name, Pheanstalk_Pheanstalk $connection, $default = false)
    {
        if(!is_bool($default)) {
            throw new \InvalidArgumentException('Default parameter have to be a boolean');
        }
        
        $this->connections[$name] = $connection;

        // Set the default connection name
        if ($default) {
            $this->default = $name;
        }
    }
}
