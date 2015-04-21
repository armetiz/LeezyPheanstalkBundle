<?php

namespace Leezy\PheanstalkBundle\Tests\Exception;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;

class PheanstalkExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exception = new PheanstalkException();

        $this->assertEquals('Pheanstalk exception.', $exception->getMessage());
    }
}
