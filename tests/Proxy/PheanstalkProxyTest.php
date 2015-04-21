<?php

namespace Leezy\PheanstalkBundle\Tests\Proxy;

use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;
use Pheanstalk\PheanstalkInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PheanstalkProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PheanstalkProxyInterface
     */
    protected $pheanstalkProxy;

    /**
     * @var PheanstalkInterface
     */
    protected $pheanstalk;

    public function setUp()
    {
        $this->pheanstalk      = $this->getMockForAbstractClass(PheanstalkInterface::class);
        $this->pheanstalkProxy = new PheanstalkProxy();
    }

    public function tearDown()
    {
        unset($this->pheanstalk);
        unset($this->pheanstalkProxy);
    }

    public function testInterfaces()
    {
        $this->assertInstanceOf(PheanstalkProxyInterface::class, $this->pheanstalkProxy);
        $this->assertInstanceOf(PheanstalkInterface::class, $this->pheanstalkProxy);
    }

    public function testProxyValue()
    {
        $this->pheanstalkProxy->setPheanstalk($this->pheanstalk);
        $this->assertEquals($this->pheanstalk, $this->pheanstalkProxy->getPheanstalk());
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
