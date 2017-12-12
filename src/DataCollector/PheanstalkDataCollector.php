<?php

namespace Leezy\PheanstalkBundle\DataCollector;

use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Exception\ServerException;
use Pheanstalk\PheanstalkInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * This is the data collector for LeezyPheanstalkBundle.
 *
 * @see    http://symfony.com/doc/current/cookbook/profiler/data_collector.html
 *
 * @author Maxime Aoustin <max44410@gmail.com>
 */
class PheanstalkDataCollector extends DataCollector
{
    /**
     * @var PheanstalkLocator
     */
    protected $pheanstalkLocator;

    /**
     * @param PheanstalkLocator $pheanstalkLocator
     */
    public function __construct(PheanstalkLocator $pheanstalkLocator)
    {
        $this->pheanstalkLocator = $pheanstalkLocator;
        $this->data              = [
            'pheanstalks' => [],
            'tubes'       => [],
            'jobCount'    => 0,
            'jobs'        => [],
        ];
    }

    public function reset()
    {
        $this->data = [
            'pheanstalks' => [],
            'tubes'       => [],
            'jobCount'    => 0,
            'jobs'        => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $defaultPheanstalk = $this->pheanstalkLocator->getDefaultPheanstalk();

        // Collect the information
        foreach ($this->pheanstalkLocator->getPheanstalks() as $name => $pheanstalk) {
            // Get information about this connection
            $this->data['pheanstalks'][$name] = [
                'name'      => $name,
                'host'      => $pheanstalk->getConnection()->getHost(),
                'port'      => $pheanstalk->getConnection()->getPort(),
                'timeout'   => $pheanstalk->getConnection()->getConnectTimeout(),
                'default'   => $defaultPheanstalk === $pheanstalk,
                'stats'     => [],
                'listening' => $pheanstalk->getConnection()->isServiceListening(),
            ];

            // If connection is not listening, there is a connection problem.
            // Skip next steps which require an established connection
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

                $this->data['tubes'][] = [
                    'pheanstalk' => $name,
                    'name'       => $tubeName,
                    'stats'      => $pheanstalk->statsTube($tubeName)->getArrayCopy(),
                ];
            }
        }
    }

    /**
     * @return array
     */
    public function getPheanstalks()
    {
        return $this->data['pheanstalks'];
    }

    /**
     * @return array
     */
    public function getTubes()
    {
        return $this->data['tubes'];
    }

    /**
     * @return int
     */
    public function getJobCount()
    {
        return $this->data['jobCount'];
    }

    /**
     * @return array
     */
    public function getJobs()
    {
        return $this->data['jobs'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pheanstalk';
    }

    /**
     * Get the next job ready and buried in the specified tube and connection.
     *
     * @param PheanstalkInterface $pheanstalk
     * @param string              $tubeName
     */
    private function fetchJobs(PheanstalkInterface $pheanstalk, $tubeName)
    {
        try {
            $nextJobReady = $pheanstalk->peekReady($tubeName);

            $this->data['jobs'][$tubeName]['ready'] = [
                'id'   => $nextJobReady->getId(),
                'data' => $nextJobReady->getData(),
            ];
        } catch (ServerException $e) {
        }

        try {
            $nextJobBuried = $pheanstalk->peekBuried($tubeName);

            $this->data['jobs'][$tubeName]['buried'] = [
                'id'   => $nextJobBuried->getId(),
                'data' => $nextJobBuried->getData(),
            ];
        } catch (ServerException $e) {
        }
    }
}
