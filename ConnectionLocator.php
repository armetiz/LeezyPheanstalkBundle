<?php

namespace Leezy\PheanstalkBundle;

use Pheanstalk_Pheanstalk;

class ConnectionLocator
{
    private $connections;
    
    public function __construct()
    {
        $this->connections = array();
    }
    
    /**
     * @return array
     */
    public function getConnections ()
    {
        return $this->connections;
    }
    
    /**
     * @param string $name
     * 
     * @return \Pheanstalk_Connection $connection
     */
    public function getConnection ($name=null) {
        $name = null === $name ? 'default' : $name;

        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }
        else {
            return null;
        }
    }
    
    /**
     * @param string $name
     * @param Pheanstalk_Pheanstalk $connection
     */
    public function addConnection($name, Pheanstalk_Pheanstalk $connection)
    {
        $this->connections[$name] = $connection;
    }
}
