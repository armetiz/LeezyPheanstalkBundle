<?php

namespace Leezy\PheanstalkBundle\Tests\Proxy;

use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;

class PheanstalkProxyTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Leezy\PheanstalkBundle\Proxy\PheanstalkProxy
     */
    protected $pheanstalkProxy;
    
    /**
     * @var \Pheanstalk_PheanstalkInterface 
     */
    protected $pheanstalk;
    
    public function setUp()
    {
        $this->pheanstalk = $this->getMock('Pheanstalk_PheanstalkInterface');
        $this->pheanstalkProxy = new PheanstalkProxy();
    }
    
    public function tearDown()
    {
        unset($this->pheanstalk);
        unset($this->pheanstalkProxy);
    }
    
    public function testInterfaces()
    {
        $this->assertInstanceOf('Leezy\PheanstalkBundle\Proxy\ProxyInterface', $this->pheanstalkProxy);
    }
    
    public function testProxyValue()
    {
        $this->pheanstalkProxy->setPheanstalk($this->pheanstalk);
        $this->assertEquals($this->pheanstalk, $this->pheanstalkProxy->getPheanstalk());
    }
    
    public function namedFunctions ()
    {
        return array (
            array('bury', array('foo', 42)),
            array('delete', array('foo')),
            array('ignore', array('foo')),
            array('kick', array(42)),
            array('listTubes'),
            array('listTubesWatched', array(true)),
            array('listTubeUsed', array(true)),
            array('pauseTube', array('foo', 42)),
            array('peek', array(42)),
            array('peekReady', array('foo')),
            array('peekDelayed', array('foo')),
            array('peekBuried', array('foo')),
            array('put', array('foo', 42, 42, 42)),
            array('putInTube', array('foo', 'bar', 42, 42, 42)),
            array('release', array('foo', 42, 42)),
            array('reserve', array(42)),
            array('reserveFromTube', array('foo', 42)),
            array('statsJob', array('foo')),
            array('statsTube', array('foo')),
            array('stats'),
            array('touch', array('foo')),
            array('useTube', array('foo')),
            array('watch', array('foo')),
            array('watchOnly', array('foo')),
        );
    }
    
    /**
     * @dataProvider namedFunctions
     */
    public function testProxyFunctionCalls($name, $value = null)
    {
        if(null === $value) {
            $value = array();
        }
        
        $pheanstalkProxy = new PheanstalkProxy();
        $pheanstalkMock = $this->getMock('Pheanstalk_PheanstalkInterface');
        $pheanstalkMock->expects($this->atLeastOnce())
                ->method($name);
        
        $pheanstalkProxy->setPheanstalk($pheanstalkMock);
        
        call_user_func_array(array($pheanstalkProxy, $name), $value);
    }
}
