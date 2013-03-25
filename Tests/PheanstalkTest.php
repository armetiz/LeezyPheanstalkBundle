<?php

namespace Leezy\PheanstalkBundle\Tests;

use Pheanstalk_Pheanstalk;

class PheanstalkTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $pheanstalk = new Pheanstalk_Pheanstalk('localhost');

        $this->assertNotNull($pheanstalk);
    }

    public function testGetConnections()
    {
        $pheanstalk = new Pheanstalk_Pheanstalk('localhost');

        $this->assertTrue(method_exists($pheanstalk, 'getConnection'));
    }
}
