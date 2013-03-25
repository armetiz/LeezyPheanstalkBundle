<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\PheanstalkLocator;

class PheanstalkLocatorTest extends \PHPUnit_Framework_TestCase {
    public function testDefaultPheanstalks()
    {
        $pheanstalkLocator = new PheanstalkLocator();
        
        $this->assertNotNull($pheanstalkLocator->getPheanstalks());
    }
    
    public function testGetNoDefinedPheanstalk()
    {
        $pheanstalk = $this->getMock('Pheanstalk_PheanstalkInterface');
        
        $pheanstalkLocator = new PheanstalkLocator();
        $pheanstalkLocator->addPheanstalk('default', $pheanstalk);
        
        $this->assertNull($pheanstalkLocator->getPheanstalk('john.doe'));
    }
    
    public function testGetDefaultPheanstalk()
    {
        $pheanstalkA = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkB = $this->getMock('Pheanstalk_PheanstalkInterface');
        
        $pheanstalkLocator = new PheanstalkLocator();
        $pheanstalkLocator->addPheanstalk('default', $pheanstalkA, true);
        $pheanstalkLocator->addPheanstalk('foo', $pheanstalkB);
        
        $this->assertEquals($pheanstalkA, $pheanstalkLocator->getPheanstalk('default'));
        $this->assertEquals($pheanstalkB, $pheanstalkLocator->getPheanstalk('foo'));
        $this->assertEquals($pheanstalkA, $pheanstalkLocator->getPheanstalk());
        $this->assertEquals($pheanstalkA, $pheanstalkLocator->getDefaultPheanstalk());
    }
}
