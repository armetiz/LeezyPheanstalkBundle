<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Pheanstalk_PheanstalkInterface;

use Leezy\PheanstalkBundle\Event\CommandEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PheanstalkProxy implements PheanstalkProxyInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $name;

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
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::BURY, new CommandEvent($this, array('job' => $job, 'priority' => $priority)));
        }

        return $this->getPheanstalk()->bury($job, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::DELETE, new CommandEvent($this, array('job' => $job)));
        }

        return $this->getPheanstalk()->delete($job);
    }

    /**
     * {@inheritDoc}
     */
    public function ignore($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::IGNORE, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->ignore($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function kick($max)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::KICK, new CommandEvent($this, array('max' => $max)));
        }

        return $this->getPheanstalk()->kick($max);
    }

    /**
     * {@inheritDoc}
     */
    public function kickJob($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::KICK_JOB, new CommandEvent($this, array('job' => $job)));
        }

        return $this->getPheanstalk()->kickJob($job);
    }

    /**
     * {@inheritDoc}
     */
    public function listTubes()
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBES, new CommandEvent($this));
        }

        return $this->getPheanstalk()->listTubes();
    }

    /**
     * {@inheritDoc}
     */
    public function listTubesWatched($askServer = false)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBES_WATCHED, new CommandEvent($this, array('askServer' => $askServer)));
        }

        return $this->getPheanstalk()->listTubesWatched($askServer);
    }

    /**
     * {@inheritDoc}
     */
    public function listTubeUsed($askServer = false)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBE_USED, new CommandEvent($this, array('askServer' => $askServer)));
        }

        return $this->getPheanstalk()->listTubeUsed($askServer);
    }

    /**
     * {@inheritDoc}
     */
    public function pauseTube($tube, $delay)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PAUSE_TUBE, new CommandEvent($this, array('tube' => $tube, 'delay' => $delay)));
        }

        return $this->getPheanstalk()->pauseTube($tube, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function peek($jobId)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK, new CommandEvent($this, array('jobId' => $jobId)));
        }

        return $this->getPheanstalk()->peek($jobId);
    }

    /**
     * {@inheritDoc}
     */
    public function peekReady($tube = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_READY, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->peekReady($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function peekDelayed($tube = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_DELAYED, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->peekDelayed($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function peekBuried($tube = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_BURIED, new CommandEvent($this, array('tube' => $tube)));
        }

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
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PUT, new CommandEvent($this, array('data' => $data,
                'priority' => $priority,
                'delay' => $delay,
                'ttr' => $ttr)));
        }

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
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PUT_IN_TUBE, new CommandEvent($this, array('tube' => $tube,
                'data' => $data,
                'priority' => $priority,
                'delay' => $delay,
                'ttr' => $ttr)));
        }

        return $this->getPheanstalk()->putInTube($tube, $data, $priority, $delay, $ttr);
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
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RELEASE, new CommandEvent($this, array('job' => $job, 'priority' => $priority, 'delay' => $delay)));
        }

        return $this->getPheanstalk()->release($job, $priority, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function reserve($timeout = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESERVE, new CommandEvent($this, array('timeout' => $timeout)));
        }

        return $this->getPheanstalk()->reserve($timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function reserveFromTube($tube, $timeout = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESERVE, new CommandEvent($this, array('tube' => $tube, 'timeout' => $timeout)));
        }

        return $this->getPheanstalk()->reserveFromTube($tube, $timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function statsJob($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS_JOB, new CommandEvent($this, array('job' => $job)));
        }

        return $this->getPheanstalk()->statsJob($job);
    }

    /**
     * {@inheritDoc}
     */
    public function statsTube($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS_TUBE, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->statsTube($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function stats()
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS, new CommandEvent($this));
        }

        return $this->getPheanstalk()->stats();
    }

    /**
     * {@inheritDoc}
     */
    public function touch($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::TOUCH, new CommandEvent($this, array('job' => $job)));
        }

        return $this->getPheanstalk()->touch($job);
    }

    /**
     * {@inheritDoc}
     */
    public function useTube($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::USE_TUBE, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->useTube($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function watch($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::WATCH, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->watch($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function watchOnly($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::WATCH_ONLY, new CommandEvent($this, array('tube' => $tube)));
        }

        return $this->getPheanstalk()->watchOnly($tube);
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatch
     */
    public function setDispatcher(EventDispatcherInterface $dispatch)
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

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
