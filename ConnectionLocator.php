<?php

namespace Leezy\PheanstalkBundle;

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
    public function getConnection ($name) {
        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }
        else {
            return null;
        }
    }
    
    /**
     * @param string $name
     * @param \Pheanstalk_Connection $connection
     * 
     * @return \Leezy\PheanstalkBundle\ConnectionLocator
     */
    public function addConnection($name, \Pheanstalk_Pheanstalk $connection)
    {
        $this->connections[$name] = $connection;
    }
}
