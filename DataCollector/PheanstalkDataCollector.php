<?php

namespace Leezy\PheanstalkBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;

use Leezy\PheanstalkBundle\PheanstalkLocator;

/**
 * This is the data collector for LeezyPheanstalkBundle
 *
 * @see http://symfony.com/doc/current/cookbook/profiler/data_collector.html
 * @author Maxime Aoustin <max44410@gmail.com>
 */
class PheanstalkDataCollector extends DataCollector
{
    protected $pheanstalkLocator;

    public function __construct(PheanstalkLocator $pheanstalkLocator)
    {
        $this->pheanstalkLocator = $pheanstalkLocator;
        $this->data = array(
            'pheanstalks' => array(),
            'tubes' => array(),
            'jobCount' => 0,
            'jobs' => array(),
        );
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $defaultPheanstalk = $this->pheanstalkLocator->getDefaultPheanstalk();
        
        // Collect the information
        foreach ($this->pheanstalkLocator->getPheanstalks() as $name => $pheanstalk) {
            // Get information about this connection
            $this->data['pheanstalks'][$name] = array(
                'name' => $name,
                'host' => $pheanstalk->getConnection()->getHost(),
                'port' => $pheanstalk->getConnection()->getPort(),
                'timeout' => $pheanstalk->getConnection()->getConnectTimeout(),
                'default' => $defaultPheanstalk === $pheanstalk,
                'stats' => array(),
                'listening' => $pheanstalk->getConnection()->isServiceListening(),
            );
            
            //If connection is not listening, there is a connection problem.
            //Skip next steps which require an established connection
            if (!$pheanstalk->getConnection()->isServiceListening()) {
                continue;
            }
            
            $pheanstalkStatistics = $pheanstalk->stats()->getArrayCopy();
            
            // Get information about this connection
            $this->data['pheanstalks'][$name]['stats'] = $pheanstalkStatistics;

            // Increment the number of jobs
            $this->data['jobCount'] += $pheanstalkStatistics['current-jobs-ready'];

            // Get information about the tubes of this connection
            $tubes = $pheanstalk->listTubes();
            foreach ($tubes as $tubeName) {

                // Fetch next ready job and next buried job for this tube
                $this->fetchJobs($pheanstalk, $tubeName);

                $this->data['tubes'][] = array(
                    'pheanstalk' => $name,
                    'name' => $tubeName,
                    'stats' => $pheanstalk->statsTube($tubeName)->getArrayCopy(),
                );
            }
        }
    }

    public function getPheanstalks()
    {
        return $this->data['pheanstalks'];
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
     * @param \Pheanstalk_Pheanstalk $pheanstalk
     * @param $tubeName
     */
    private function fetchJobs(PheanstalkProxyInterface $pheanstalk, $tubeName) {
        try {
            $nextJobReady = $pheanstalk->peekReady($tubeName);
            $this->data['jobs'][$tubeName]['ready'] = array(
                'id' => $nextJobReady->getId(),
                'data' => $nextJobReady->getData(),
            );
        }catch (\Pheanstalk_Exception_ServerException $e){}

        try {
            $nextJobBuried = $pheanstalk->peekBuried($tubeName);
            $this->data['jobs'][$tubeName]['buried'] = array(
                'id' => $nextJobBuried->getId(),
                'data' => $nextJobBuried->getData(),
            );
        }catch (\Pheanstalk_Exception_ServerException $e){}
    }
}
