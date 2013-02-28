<?php

namespace Leezy\PheanstalkBundle;

use Pheanstalk_Pheanstalk;

class ConnectionLocator
{
    private $connections;
    private $info;
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
        $name = null !== $name ?: $this->default;

        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getConnectionsInfo()
    {
        return $this->info;
    }
    
    /**
     * @param string $name
     * @param Pheanstalk_Pheanstalk $connection
     * @param array $info
     */
    public function addConnection($name, Pheanstalk_Pheanstalk $connection, array $info)
    {
        $this->connections[$name] = $connection;
        $this->info[$name] = array_merge($info, array('resource' => $connection));

        if (true === $info['default']) {
            $this->default = $name;
        }
    }
}
