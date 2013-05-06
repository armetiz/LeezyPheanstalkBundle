<?php

namespace Leezy\PheanstalkBundle\Tests\Listener;

use Leezy\PheanstalkBundle\Listener\PheanstalkLogListener;

class PheanstalkLogListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogger()
    {
        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        
        $pheanstalkLogListener = new PheanstalkLogListener();
        $this->assertNull($pheanstalkLogListener->getLogger());
        $this->assertEquals($pheanstalkLogListener, $pheanstalkLogListener->setLogger($logger));
        $this->assertEquals($logger, $pheanstalkLogListener->getLogger());
        
        $this->assertTrue(method_exists($logger, 'addWarning'));
        $this->assertTrue(method_exists($logger, 'addInfo'));
    }
}
