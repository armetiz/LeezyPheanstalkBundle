<?php

namespace Leezy\PheanstalkBundle\Profiler\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Leezy\PheanstalkBundle\ConnectionLocator;

class PheanstalkDataCollector extends DataCollector
{
    protected $pool;

    protected $connections = array();
    protected $tubes = array();
    protected $jobs = array();

    public function __construct(ConnectionLocator $connectionLocator)
    {
        $this->pool = $connectionLocator;
        $this->connections = $this->pool->getConnections();
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // Collect the information
        $this->connections = $this->pool->getConnections();
        foreach ($this->connections as $name => $connection) {
            $this->tubes[$name] = $connection->listTubes();
        }
    }

    public function getConnections()
    {
        return $this->connections;
    }


    public function getName()
    {
        return 'pheanstalk';
    }
}
