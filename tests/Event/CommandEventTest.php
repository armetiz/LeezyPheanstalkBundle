<?php

namespace Leezy\PheanstalkBundle\Tests\Event;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Pheanstalk\PheanstalkInterface;
use PHPUnit\Framework\TestCase;

class CommandEventTest extends TestCase
{
    public function testCommandEvent()
    {
        $pheanstalk = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $payload = ['foo'];

        $event = new CommandEvent($pheanstalk, $payload);

        $this->assertSame($pheanstalk, $event->getPheanstalk());
        $this->assertSame($payload, $event->getPayload());
    }
}
