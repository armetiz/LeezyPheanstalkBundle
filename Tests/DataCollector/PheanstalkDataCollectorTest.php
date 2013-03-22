<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\ConnectionLocator;
use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;

class PheanstalkDataCollectorTest extends \PHPUnit_Framework_TestCase {
    public function testCollect()
    {
        $pheanstalkA = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkB = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkC = $this->getMock('Pheanstalk_PheanstalkInterface');

        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $pheanstalkA, true);
        $connectionLocator->addConnection('foo', $pheanstalkB);
        $connectionLocator->addConnection('bar', $pheanstalkC);
        
        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")->disableOriginalConstructor()->getMock();
        $response = $this->getMockBuilder("Symfony\Component\HttpFoundation\Response")->disableOriginalConstructor()->getMock();

        $dataCollector = new PheanstalkDataCollector($connectionLocator);
        //$dataCollector->collect($request, $response);
        
        
        //$this->assertNotNull($pheanstalk);
    }
}
