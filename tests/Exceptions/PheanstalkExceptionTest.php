<?php

namespace Leezy\PheanstalkBundle\Tests\Exception;

use Leezy\PheanstalkBundle\Exceptions\PheanstalkException;
use PHPUnit\Framework\TestCase;

class PheanstalkExceptionTest extends TestCase
{
    public function testConstructor()
    {
        $exception = new PheanstalkException();

        $this->assertEquals('Pheanstalk exception.', $exception->getMessage());
    }
}
