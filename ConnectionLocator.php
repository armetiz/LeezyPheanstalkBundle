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
        $name = null === $name ? $this->default : $name;

        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name]['resource'];
        }
        else {
            return null;
        }
    }
    
    /**
     * @param string $name
     * @param Pheanstalk_Pheanstalk $connection
     * @param bool $is_default
     */
    public function addConnection($name, Pheanstalk_Pheanstalk $connection, $is_default = false)
    {
        $this->connections[$name] = array('resource' => $connection, 'default' => $is_default);
        if (true === $is_default) {
            $this->default = $name;
        }
    }
}
