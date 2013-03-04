<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\ConnectionLocator;
use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;

class PheanstalkDataCollectorTest extends \PHPUnit_Framework_TestCase {
    public function testCollect()
    {
        $connectionA = $this->getMockBuilder("Pheanstalk_Pheanstalk")->disableOriginalConstructor()->getMock();
        $connectionB = $this->getMockBuilder("Pheanstalk_Pheanstalk")->disableOriginalConstructor()->getMock();
        $connectionC = $this->getMockBuilder("Pheanstalk_Pheanstalk")->disableOriginalConstructor()->getMock();

        $connectionLocator = new ConnectionLocator();
        $connectionLocator->addConnection('default', $connectionA, true);
        $connectionLocator->addConnection('foo', $connectionB);
        $connectionLocator->addConnection('bar', $connectionC);
        
        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")->disableOriginalConstructor()->getMock();
        $response = $this->getMockBuilder("Symfony\Component\HttpFoundation\Response")->disableOriginalConstructor()->getMock();

        $dataCollector = new PheanstalkDataCollector($connectionLocator);
        //$dataCollector->collect($request, $response);
        
        
        //$this->assertNotNull($pheanstalk);
    }
}
