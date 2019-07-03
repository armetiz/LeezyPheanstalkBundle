<?php

namespace Leezy\PheanstalkBundle\Tests\Proxy;

use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;
use Pheanstalk\Connection;
use Pheanstalk\Contract\PheanstalkInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PheanstalkProxyTest extends TestCase
{
    /**
     * @var PheanstalkProxyInterface
     */
    protected $pheanstalkProxy;

    /**
     * @var PheanstalkInterface
     */
    protected $pheanstalk;

    public function setUp(): void
    {
        $this->pheanstalk      = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $this->pheanstalkProxy = new PheanstalkProxy(
            'default',
            $this->getMockForAbstractClass(PheanstalkInterface::class),
            $this->getMockForClass(Connection::class)
        );
    }

    public function tearDown(): void
    {
        unset($this->pheanstalk);
        unset($this->pheanstalkProxy);
    }

    public function testInterfaces()
    {
        $this->assertInstanceOf(PheanstalkInterface::class, $this->pheanstalk);
        $this->assertInstanceOf(PheanstalkProxyInterface::class, $this->pheanstalkProxy);
    }

    public function namedFunctions()
    {
        return [
            ['bury', ['foo', 42]],
            ['delete', ['foo']],
            ['ignore', ['foo']],
            ['kick', [42]],
            ['listTubes'],
            ['listTubesWatched', [true]],
            ['listTubeUsed', [true]],
            ['pauseTube', ['foo', 42]],
            ['peek', [42]],
            ['peekReady', ['foo']],
            ['peekDelayed', ['foo']],
            ['peekBuried', ['foo']],
            ['put', ['foo', 42, 42, 42]],
            ['putInTube', ['foo', 'bar', 42, 42, 42]],
            ['release', ['foo', 42, 42]],
            ['reserve', [42]],
            ['reserveFromTube', ['foo', 42]],
            ['statsJob', ['foo']],
            ['statsTube', ['foo']],
            ['stats'],
            ['touch', ['foo']],
            ['useTube', ['foo']],
            ['watch', ['foo']],
            ['watchOnly', ['foo']],
        ];
    }

    /**
     * @dataProvider namedFunctions
     */
    public function testProxyFunctionCalls($name, $value = null)
    {
        if (null === $value) {
            $value = [];
        }

        $pheanstalkProxy = new PheanstalkProxy();
        $pheanstalkMock  = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $dispatchMock    = $this->getMockForAbstractClass(EventDispatcherInterface::class);
        $pheanstalkMock->expects($this->atLeastOnce())->method($name);

        $pheanstalkProxy->setPheanstalk($pheanstalkMock);
        $pheanstalkProxy->setDispatcher($dispatchMock);

        call_user_func_array([$pheanstalkProxy, $name], $value);
    }
}
