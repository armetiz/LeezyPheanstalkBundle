<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Connection;
use Pheanstalk\PheanstalkInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PheanstalkDataCollectorTest extends TestCase
{
    public function testCollect()
    {
        $pheanstalkConnection = $this->getMockBuilder(Connection::class)
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

        $pheanstalkA = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $pheanstalkB = $this->getMockForAbstractClass(PheanstalkInterface::class);

        $pheanstalkA->expects($this->any())->method('getConnection')->will($this->returnValue($pheanstalkConnection));
        $pheanstalkB->expects($this->any())->method('getConnection')->will($this->returnValue($pheanstalkConnection));

        $pheanstalkLocator = new PheanstalkLocator();
        $pheanstalkLocator->addPheanstalk('default', $pheanstalkA, true);
        $pheanstalkLocator->addPheanstalk('foo', $pheanstalkB);

        $request  = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();

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
