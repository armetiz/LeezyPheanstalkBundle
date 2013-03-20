<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Pheanstalk_PheanstalkInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PheanstalkProxy implements ProxyInterface {
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;
    
    /**
     * @var \Pheanstalk_PheanstalkInterface 
     */
    protected $pheanstalk;
    
    /**
     * {@inheritDoc}
     */
    public function setConnection(Pheanstalk_Connection $connection)
    {
        return $this->getPheanstalk()->setConnection($connection);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection()
    {
        return $this->getPheanstalk()->getConnection();
    }

    // ----------------------------------------

    /**
     * {@inheritDoc}
     */
    public function bury($job, $priority = Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY)
    {
        $this->getPheanstalk()->bury($job, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($job)
    {
        $this->getPheanstalk()->delete($job);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function ignore($tube)
    {
        $this->getPheanstalk()->ignore($tube);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function kick($max)
    {
        return $this->getPheanstalk()->kick($max);
    }

    /**
     * {@inheritDoc}
     */
    public function listTubes()
    {
        return $this->getPheanstalk()->listTubes();
    }

    /**
     * {@inheritDoc}
     */
    public function listTubesWatched($askServer = false)
    {
        return $this->getPheanstalk()->listTubesWatched($askServer);
    }

    /**
     * {@inheritDoc}
     */
    public function listTubeUsed($askServer = false)
    {
        return $this->getPheanstalk()->listTubeUsed($askServer);
    }

    /**
     * {@inheritDoc}
     */
    public function pauseTube($tube, $delay)
    {
        return $this->getPheanstalk()->pauseTube($tube, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function peek($jobId)
    {
        return $this->getPheanstalk()->peek($jobId);
    }

    /**
     * {@inheritDoc}
     */
    public function peekReady($tube = null)
    {
        return $this->getPheanstalk()->peekReady($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function peekDelayed($tube = null)
    {
        return $this->getPheanstalk()->peekDelayed($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function peekBuried($tube = null)
    {
        return $this->getPheanstalk()->peekBuried($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function put(
        $data,
        $priority = Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY,
        $delay = Pheanstalk_PheanstalkInterface::DEFAULT_DELAY,
        $ttr = Pheanstalk_PheanstalkInterface::DEFAULT_TTR
    )
    {
        return $this->getPheanstalk()->put($data, $priority, $delay, $ttr);
    }

    /**
     * {@inheritDoc}
     */
    public function putInTube(
        $tube,
        $data,
        $priority = Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY,
        $delay = Pheanstalk_PheanstalkInterface::DEFAULT_DELAY,
        $ttr = Pheanstalk_PheanstalkInterface::DEFAULT_TTR
    )
    {
        $this->getPheanstalk()->putInTube($tube, $data, $priority, $delay, $ttr);
    }

    /**
     * {@inheritDoc}
     */
    public function release(
        $job,
        $priority = Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY,
        $delay = Pheanstalk_PheanstalkInterface::DEFAULT_DELAY
    )
    {
        return $this->getPheanstalk()->release($job, $priority, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function reserve($timeout = null)
    {
        return $this->getPheanstalk()->reserve($timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function reserveFromTube($tube, $timeout = null)
    {
        return $this->getPheanstalk()->reserveFromTube($tube, $timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function statsJob($job)
    {
        return $this->getPheanstalk()->statsJob($job);
    }

    /**
     * {@inheritDoc}
     */
    public function statsTube($tube)
    {
        return $this->getPheanstalk()->statsTube($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function stats()
    {
        return $this->getPheanstalk()->stats();
    }

    /**
     * {@inheritDoc}
     */
    public function touch($job)
    {
        return $this->getPheanstalk()->touch($job);
    }

    /**
     * {@inheritDoc}
     */
    public function useTube($tube)
    {
        return $this->getPheanstalk()->useTube($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function watch($tube)
    {
        return $this->getPheanstalk()->watch($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function watchOnly($tube)
    {
        return $this->getPheanstalk()->watchOnly($tube);
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatch()
    {
        return $this->dispatcher;
    }
    
    /**
     * 
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatch
     */
    public function setDispatch(EventDispatcherInterface $dispatch)
    {
        $this->dispatcher = $dispatch;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setPheanstalk(Pheanstalk_PheanstalkInterface $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }
}
