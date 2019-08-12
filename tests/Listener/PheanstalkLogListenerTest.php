<?php

namespace Leezy\PheanstalkBundle\Tests\Listener;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Leezy\PheanstalkBundle\Listener\PheanstalkLogListener;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Connection;
use Pheanstalk\Contract\PheanstalkInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PheanstalkLogListenerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|PheanstalkInterface */
    protected $pheanstalk;

    /** @var \PHPUnit_Framework_MockObject_MockObject|PheanstalkLocator */
    protected $locator;

    /** @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface */
    protected $logger;

    protected function setUp(): void
    {
        $this->logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->setMethods(['info', 'warning'])
            ->getMockForAbstractClass()
        ;

        $this->locator = $this
            ->getMockBuilder(PheanstalkLocator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPheanstalkName'])
            ->getMock()
        ;

        $this->pheanstalk = $this
            ->getMockBuilder(PheanstalkInterface::class)
            ->getMockForAbstractClass()
        ;
    }

    public function testNoLogger()
    {
        $this->logger->expects($this->never())->method('info');

        $listener = new PheanstalkLogListener($this->locator);
        $listener->onCommand(new CommandEvent($this->pheanstalk, []), CommandEvent::PEEK_READY);
    }

    public function testLogger()
    {
        $this->logger->expects($this->once())->method('info');

        $listener = new PheanstalkLogListener($this->locator);
        $listener->setLogger($this->logger);
        $listener->onCommand(new CommandEvent($this->pheanstalk, []), CommandEvent::PEEK_READY);
    }

    /**
     * @see https://github.com/armetiz/LeezyPheanstalkBundle/issues/60
     */
    public function testWithEventDispatcher()
    {
        $this->logger->expects($this->once())->method('info');

        $listener = new PheanstalkLogListener($this->locator);
        $listener->setLogger($this->logger);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($listener);
        $eventDispatcher->dispatch(
            CommandEvent::PEEK_READY,
            new CommandEvent($this->pheanstalk, [])
        );
    }
}
