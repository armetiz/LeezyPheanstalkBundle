<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\PheanstalkLocator;
use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;

class PheanstalkDataCollectorTest extends \PHPUnit_Framework_TestCase {
    public function testCollect()
    {
        $pheanstalkA = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkB = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkC = $this->getMock('Pheanstalk_PheanstalkInterface');

        $pheanstalkLocator = new PheanstalkLocator();
        $pheanstalkLocator->addPheanstalk('default', $pheanstalkA, true);
        $pheanstalkLocator->addPheanstalk('foo', $pheanstalkB);
        $pheanstalkLocator->addPheanstalk('bar', $pheanstalkC);
        
        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")->disableOriginalConstructor()->getMock();
        $response = $this->getMockBuilder("Symfony\Component\HttpFoundation\Response")->disableOriginalConstructor()->getMock();

        $dataCollector = new PheanstalkDataCollector($pheanstalkLocator);
        //$dataCollector->collect($request, $response);
        
        
        //$this->assertNotNull($pheanstalk);
    }
}
