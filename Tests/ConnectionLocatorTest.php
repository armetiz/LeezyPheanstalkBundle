<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\ConnectionLocator;

class ConnectionLocatorTest extends \PHPUnit_Framework_TestCase {
    public function testDefaultConnections()
    {
        $connectionLocator = new ConnectionLocator();
        
        $this->assertNotNull($connectionLocator->getConnections());
    }
    
    public function testGetNoDefinedConnection()
    {
        $connection = new \Pheanstalk_Pheanstalk('localhost', '11300');
        
        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $connection);
        
        $this->assertNull($connectionLocator->getConnection('john.doe'));
    }
    
    public function testGetDefaultConnection()
    {
        $connectionA = new \Pheanstalk_Pheanstalk('localhost', '11300');
        $connectionB = new \Pheanstalk_Pheanstalk('localhost', '11300');
        
        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $connectionA, true);
        $connectionLocator->addConnection('foo', $connectionB);
        
        $this->assertEquals($connectionA, $connectionLocator->getConnection('default'));
        $this->assertEquals($connectionB, $connectionLocator->getConnection('foo'));
        $this->assertEquals($connectionA, $connectionLocator->getConnection());
        $this->assertEquals($connectionA, $connectionLocator->getDefaultConnection());
    }
}
