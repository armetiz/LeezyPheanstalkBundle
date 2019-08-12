<?php

namespace Leezy\PheanstalkBundle\Tests;

use Leezy\PheanstalkBundle\DataCollector\PheanstalkDataCollector;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Response\ArrayResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PheanstalkDataCollectorTest extends TestCase
{
    public function testCollect()
    {
        $emptyStatistics = new ArrayResponse('STATS', [
            'current-jobs-ready' => 0,
        ]);

        $pheanstalkA = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $pheanstalkB = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $pheanstalkA->expects($this->once())->method('stats')->willReturn($emptyStatistics);
        $pheanstalkB->expects($this->once())->method('stats')->willReturn($emptyStatistics);

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
        $this->assertArrayHasKey('default', $data['default']);
        $this->assertArrayHasKey('stats', $data['default']);
        $this->assertEquals(0, $data['default']['jobCount']);
    }
}
