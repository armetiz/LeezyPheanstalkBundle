<?php

namespace Leezy\PheanstalkBundle\Exceptions;

class PheanstalkException extends \Exception
{
    /**
     * @inheritdoc
     */
    public function __construct($message = 'Pheanstalk exception.')
    {
        parent::__construct($message);
    }
}
