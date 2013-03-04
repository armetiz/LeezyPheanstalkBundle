<?php

namespace Leezy\PheanstalkBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pheanstalk_Pheanstalk;

use Leezy\PheanstalkBundle\ConnectionLocator;

/**
 * This is the data collector for LeezyPheanstalkBundle
 *
 * @see http://symfony.com/doc/current/cookbook/profiler/data_collector.html
 * @author Maxime Aoustin <max44410@gmail.com>
 */
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
        $defaultConnection = $this->connectionLocator->getDefaultConnection();
        
        // Collect the information
        foreach ($this->connectionLocator->getConnections() as $name => $connection) {
            $connectionInformations = $connection->getConnection();
            $connectionStatistics = $connection->stats()->getArrayCopy();
            
            // Get information about this connection
            $this->data['connections'][] = array(
                'name' => $name,
                'host' => $connectionInformations->getHost(),
                'port' => $connectionInformations->getPort(),
                'timeout' => $connectionInformations->getConnectTimeout(),
                'default' => $defaultConnection === $connection,
                'stats' => $connectionStatistics
            );

            // Increment the number of jobs
            $this->data['jobCount'] += $connectionStatistics['current-jobs-ready'];

            // Get information about the tubes of this connection
            $tubes = $connection['resource']->listTubes();
            foreach ($tubes as $tubeName) {

                // Fetch next ready job and next buried job for this tube
                $this->fetchJobs($connection['resource'], $tubeName);

                $this->data['tubes'][] = array(
                    'connection' => $name,
                    'name' => $tubeName,
                    'stats' => $connection['resource']->statsTube($tubeName)->getArrayCopy(),
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

    /**
     * Get the next job ready and buried in the specified tube and connection
     *
     * @param \Pheanstalk_Pheanstalk $connection
     * @param $tubeName
     */
    private function fetchJobs(Pheanstalk_Pheanstalk $connection, $tubeName) {
        try {
            $nextJobReady = $connection->peekReady($tubeName);
            $this->data['jobs'][$tubeName]['ready'] = array(
                'id' => $nextJobReady->getId(),
                'data' => $nextJobReady->getData(),
            );
        }catch (\Pheanstalk_Exception_ServerException $e){}

        try {
            $nextJobBuried = $connection->peekBuried($tubeName);
            $this->data['jobs'][$tubeName]['buried'] = array(
                'id' => $nextJobBuried->getId(),
                'data' => $nextJobBuried->getData(),
            );
        }catch (\Pheanstalk_Exception_ServerException $e){}
    }
}
