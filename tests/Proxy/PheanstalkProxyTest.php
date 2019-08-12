<?php

namespace Leezy\PheanstalkBundle\Tests\Proxy;

use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;
use Pheanstalk\Connection;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\JobId;
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
            $this->getMockForAbstractClass(PheanstalkInterface::class)
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
            ['bury', [new JobId(42)]],
            ['delete', [new JobId(42)]],
            ['ignore', ['foo']],
            ['kick', [42]],
            ['listTubes'],
            ['listTubesWatched', [true]],
            ['listTubeUsed', [true]],
            ['pauseTube', ['foo', 42]],
            ['peek', [new JobId(42)]],
            ['peekReady', ['foo']],
            ['peekDelayed', ['foo']],
            ['peekBuried', ['foo']],
            ['put', ['foo', 42, 42, 42]],
            ['release', [new JobId(42)]],
            ['reserve', [42]],
            ['statsJob', [new JobId(42)]],
            ['statsTube', ['foo']],
            ['stats'],
            ['touch', [new JobId(42)]],
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

        $pheanstalkMock  = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $dispatchMock    = $this->getMockForAbstractClass(EventDispatcherInterface::class);
        $pheanstalkMock->expects($this->atLeastOnce())->method($name);

        $pheanstalkProxy = new PheanstalkProxy($pheanstalkMock);
        $pheanstalkProxy->setDispatcher($dispatchMock);

        call_user_func_array([$pheanstalkProxy, $name], $value);
    }
}
