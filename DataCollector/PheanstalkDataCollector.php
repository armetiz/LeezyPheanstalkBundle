<?php

namespace Leezy\PheanstalkBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Leezy\PheanstalkBundle\ConnectionLocator;

class PheanstalkDataCollector extends DataCollector
{
    protected $connectionLocator;

    public function __construct(ConnectionLocator $connectionLocator)
    {
        $this->connectionLocator = $connectionLocator;
        $this->data = array(
            'connections' => array(),
            'tubes' => array(),
            'jobCount' => 0,
            'jobs' => array(),
        );
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // Collect the information
        foreach ($this->connectionLocator->getConnections() as $name => $connection) {
            $connection_stats = $connection['resource']->stats();

            // Get information about this connection
            $this->data['connections'][] = array(
                'name' => $name,
                'default' => $connection['default'],
                'stats' => $connection_stats->getArrayCopy()
            );

            $this->data['jobCount'] += $connection_stats->getArrayCopy()['current-jobs-ready'];

            // Get information about the tubes of this connection
            $tubes = $connection['resource']->listTubes();
            foreach ($tubes as $tube_name) {

                // Fetch next ready job and next buried job for this tube
                $this->fetchJobs($connection['resource'], $tube_name);

                $this->data['tubes'][] = array(
                    'connection' => $name,
                    'name' => $tube_name,
                    'stats' => $connection['resource']->statsTube($tube_name)->getArrayCopy(),
                );
            }
        }
    }

    public function getConnections()
    {
        return $this->data['connections'];
    }

    public function getTubes()
    {
        return $this->data['tubes'];
    }

    public function getJobCount()
    {
        return $this->data['jobCount'];
    }

    public function getJobs()
    {
        return $this->data['jobs'];
    }

    public function getName()
    {
        return 'pheanstalk';
    }

    private function fetchJobs($cnx, $tube_name) {
        try {
            $next_ready_job = $cnx->peekReady($tube_name);
            $this->data['jobs'][$tube_name]['ready'] = array(
                'id' => $next_ready_job->getId(),
                'data' => $next_ready_job->getData(),
            );
        }catch (\Pheanstalk_Exception_ServerException $e){}

        try {
            $next_ready_job = $cnx->peekBuried($tube_name);
            $this->data['jobs'][$tube_name]['buried'] = array(
                'id' => $next_ready_job->getId(),
                'data' => $next_ready_job->getData(),
            );
        }catch (\Pheanstalk_Exception_ServerException $e){}
    }
}
