<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\PheanstalkLocator;
use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;

class PheanstalkDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollect()
    {
        $pheanstalkConnection = $this->getMockBuilder('Pheanstalk_Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $pheanstalkConnection
            ->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.1'));

        $pheanstalkConnection
            ->expects($this->any())
            ->method('getPort')
            ->will($this->returnValue('11130'));

        $pheanstalkConnection
            ->expects($this->any())
            ->method('getConnectTimeout')
            ->will($this->returnValue(60));

        $pheanstalkConnection
            ->expects($this->any())
            ->method('isServiceListening')
            ->will($this->returnValue(false));

        $pheanstalkA = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkB = $this->getMock('Pheanstalk_PheanstalkInterface');

        $pheanstalkA->expects($this->any())->method('getConnection')->will($this->returnValue($pheanstalkConnection));
        $pheanstalkB->expects($this->any())->method('getConnection')->will($this->returnValue($pheanstalkConnection));

        $pheanstalkLocator = new PheanstalkLocator();
        $pheanstalkLocator->addPheanstalk('default', $pheanstalkA, true);
        $pheanstalkLocator->addPheanstalk('foo', $pheanstalkB);

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")->disableOriginalConstructor()->getMock();
        $response = $this->getMockBuilder("Symfony\Component\HttpFoundation\Response")->disableOriginalConstructor()->getMock();

        $dataCollector = new PheanstalkDataCollector($pheanstalkLocator);
        $dataCollector->collect($request, $response);

        $this->assertArrayHasKey('default', $dataCollector->getPheanstalks());
        $this->assertArrayHasKey('foo', $dataCollector->getPheanstalks());
        $this->assertArrayNotHasKey('bar', $dataCollector->getPheanstalks());

        $data = $dataCollector->getPheanstalks();

        $this->assertArrayHasKey('name', $data['default']);
        $this->assertArrayHasKey('host', $data['default']);
        $this->assertArrayHasKey('port', $data['default']);
        $this->assertArrayHasKey('timeout', $data['default']);
        $this->assertArrayHasKey('default', $data['default']);
        $this->assertArrayHasKey('stats', $data['default']);
        $this->assertArrayHasKey('listening', $data['default']);
    }
}
