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
        $pheanstalk = $this->getMock('Pheanstalk_PheanstalkInterface');
        
        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $pheanstalk);
        
        $this->assertNull($connectionLocator->getConnection('john.doe'));
    }
    
    public function testGetDefaultConnection()
    {
        $pheanstalkA = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkB = $this->getMock('Pheanstalk_PheanstalkInterface');
        
        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $pheanstalkA, true);
        $connectionLocator->addConnection('foo', $pheanstalkB);
        
        $this->assertEquals($pheanstalkA, $connectionLocator->getConnection('default'));
        $this->assertEquals($pheanstalkB, $connectionLocator->getConnection('foo'));
        $this->assertEquals($pheanstalkA, $connectionLocator->getConnection());
        $this->assertEquals($pheanstalkA, $connectionLocator->getDefaultConnection());
    }
}
