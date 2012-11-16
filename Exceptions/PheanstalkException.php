<?php

namespace Leezy\PheanstalkBundle\Exceptions;

use Exception;

class PheanstalkException extends Exception {

    public function __construct($message = "Pheanstalk exception.") {
        parent::__construct($message);
    }

}