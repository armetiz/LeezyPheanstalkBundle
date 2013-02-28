<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\ConnectionLocator;

class ConnectionLocatorTest extends \PHPUnit_Framework_TestCase {
    public function testDefaultConnections()
    {
        $connectionLocator = new ConnectionLocator();
        
        $this->assertNotNull($connectionLocator->getConnections());
    }
    
    public function testGetDefaultConnection()
    {
        $connection = new \Pheanstalk_Pheanstalk('localhost', '11300');
        
        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $connection);
        
        $this->assertEquals($connection, $connectionLocator->getConnection(null));
    }
    
    public function testGetNoDefinedConnection()
    {
        $connection = new \Pheanstalk_Pheanstalk('localhost', '11300');
        
        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $connection);
        
        $this->assertNull($connectionLocator->getConnection('john.doe'));
    }
}
