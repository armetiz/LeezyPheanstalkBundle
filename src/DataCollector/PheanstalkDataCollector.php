<?php

namespace Leezy\PheanstalkBundle\DataCollector;

use Throwable;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Exception\ConnectionException;
use Pheanstalk\Exception\ServerException;
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
    use DataCollectorBCTrait;

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
        $this->data = [
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

    protected function doCollect(Request $request, Response $response, Throwable $exception = null)
    {
        $defaultPheanstalk = $this->pheanstalkLocator->getDefaultPheanstalk();

        // Collect the information
        /** @var PheanstalkInterface $pheanstalk */
        foreach ($this->pheanstalkLocator->getPheanstalks() as $name => $pheanstalk) {
            // Get information about this connection
            $this->data['pheanstalks'][$name] = [
                'name'    => $name,
                'default' => $defaultPheanstalk === $pheanstalk,
                'stats'   => [],
            ];

            try {
                $pheanstalkStatistics = iterator_to_array($pheanstalk->stats());
            } catch (ConnectionException $exception) {
                // If stats() fails with a ConnectionException the following pheanstalk operations will (probably) fail as well.
                continue;
            }

            // Get information about this connection
            $this->data['pheanstalks'][$name]['stats'] = $pheanstalkStatistics;

            // Increment the number of jobs
            $this->data['jobCount'] += $pheanstalkStatistics['current-jobs-ready'];

            // Get information about the tubes of this connection
            $tubes = $pheanstalk->listTubes();
            foreach ($tubes as $tubeName) {
                // Fetch next ready job and next buried job for this tube
                $this->fetchJobs($pheanstalk, $tubeName);

                try {
                    $tubeStatistics = iterator_to_array($pheanstalk->statsTube($tubeName));
                } catch (ConnectionException $exception) {
                    $tubeStatistics = null;
                }

                $this->data['tubes'][] = [
                    'pheanstalk' => $name,
                    'name'       => $tubeName,
                    'stats'      => $tubeStatistics,
                ];
            }
        }
    }

    /**
     * Get the next job ready and buried in the specified tube and connection.
     *
     * @param PheanstalkInterface $pheanstalk
     * @param string              $tubeName
     */
    private function fetchJobs(PheanstalkInterface $pheanstalk, string $tubeName): void
    {
        try {
            $nextJobReady = $pheanstalk->useTube($tubeName)->peekReady();

            if (null === $nextJobReady) {
                return;
            }

            $this->data['jobs'][$tubeName]['ready'] = [
                'id'   => $nextJobReady->getId(),
                'data' => $nextJobReady->getData(),
            ];
        } catch (ServerException $e) {
        }

        try {
            $nextJobBuried = $pheanstalk->useTube($tubeName)->peekBuried();

            if (null === $nextJobBuried) {
                return;
            }

            $this->data['jobs'][$tubeName]['buried'] = [
                'id'   => $nextJobBuried->getId(),
                'data' => $nextJobBuried->getData(),
            ];
        } catch (ServerException $e) {
        }
    }
}
