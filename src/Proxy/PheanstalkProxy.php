<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Pheanstalk\Connection;
use Pheanstalk\PheanstalkInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PheanstalkProxy implements PheanstalkProxyInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var PheanstalkInterface
     */
    protected $pheanstalk;

    /**
     * {@inheritDoc}
     */
    public function setConnection(Connection $connection)
    {
        $this->pheanstalk->setConnection($connection);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection()
    {
        return $this->pheanstalk->getConnection();
    }

    /**
     * {@inheritDoc}
     */
    public function bury($job, $priority = self::DEFAULT_PRIORITY)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::BURY, new CommandEvent($this, ['job' => $job, 'priority' => $priority]));
        }

        $this->pheanstalk->bury($job, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::DELETE, new CommandEvent($this, ['job' => $job]));
        }

        $this->pheanstalk->delete($job);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function ignore($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::IGNORE, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->ignore($tube);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function kick($max)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::KICK, new CommandEvent($this, ['max' => $max]));
        }

        return $this->pheanstalk->kick($max);
    }

    /**
     * {@inheritDoc}
     */
    public function kickJob($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::KICK_JOB, new CommandEvent($this, ['job' => $job]));
        }

        $this->pheanstalk->kickJob($job);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function listTubes()
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBES, new CommandEvent($this));
        }

        return $this->pheanstalk->listTubes();
    }

    /**
     * {@inheritDoc}
     */
    public function listTubesWatched($askServer = false)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBES_WATCHED, new CommandEvent($this, ['askServer' => $askServer]));
        }

        return $this->pheanstalk->listTubesWatched($askServer);
    }

    /**
     * {@inheritDoc}
     */
    public function listTubeUsed($askServer = false)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBE_USED, new CommandEvent($this, ['askServer' => $askServer]));
        }

        return $this->pheanstalk->listTubeUsed($askServer);
    }

    /**
     * {@inheritDoc}
     */
    public function pauseTube($tube, $delay)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PAUSE_TUBE, new CommandEvent($this, ['tube' => $tube, 'delay' => $delay]));
        }

        $this->pheanstalk->pauseTube($tube, $delay);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function resumeTube($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESUME_TUBE, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->resumeTube($tube);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function peek($jobId)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK, new CommandEvent($this, ['jobId' => $jobId]));
        }

        return $this->pheanstalk->peek($jobId);
    }

    /**
     * {@inheritDoc}
     */
    public function peekReady($tube = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_READY, new CommandEvent($this, ['tube' => $tube]));
        }

        return $this->pheanstalk->peekReady($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function peekDelayed($tube = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_DELAYED, new CommandEvent($this, ['tube' => $tube]));
        }

        return $this->pheanstalk->peekDelayed($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function peekBuried($tube = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_BURIED, new CommandEvent($this, ['tube' => $tube]));
        }

        return $this->pheanstalk->peekBuried($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function put($data, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY, $ttr = self::DEFAULT_TTR)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                CommandEvent::PUT,
                new CommandEvent(
                    $this,
                    [
                        'data'     => $data,
                        'priority' => $priority,
                        'delay'    => $delay,
                        'ttr'      => $ttr,
                    ]
                )
            );
        }

        return $this->pheanstalk->put($data, $priority, $delay, $ttr);
    }

    /**
     * {@inheritDoc}
     */
    public function putInTube($tube, $data, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY, $ttr = self::DEFAULT_TTR)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                CommandEvent::PUT_IN_TUBE,
                new CommandEvent(
                    $this,
                    [
                        'tube'     => $tube,
                        'data'     => $data,
                        'priority' => $priority,
                        'delay'    => $delay,
                        'ttr'      => $ttr,
                    ]
                )
            );
        }

        return $this->pheanstalk->putInTube($tube, $data, $priority, $delay, $ttr);
    }

    /**
     * {@inheritDoc}
     */
    public function release($job, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RELEASE, new CommandEvent($this, ['job' => $job, 'priority' => $priority, 'delay' => $delay]));
        }

        $this->pheanstalk->release($job, $priority, $delay);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function reserve($timeout = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESERVE, new CommandEvent($this, ['timeout' => $timeout]));
        }

        return $this->pheanstalk->reserve($timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function reserveFromTube($tube, $timeout = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESERVE, new CommandEvent($this, ['tube' => $tube, 'timeout' => $timeout]));
        }

        return $this->pheanstalk->reserveFromTube($tube, $timeout);
    }

    /**
     * {@inheritDoc}
     */
    public function statsJob($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS_JOB, new CommandEvent($this, ['job' => $job]));
        }

        return $this->pheanstalk->statsJob($job);
    }

    /**
     * {@inheritDoc}
     */
    public function statsTube($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS_TUBE, new CommandEvent($this, ['tube' => $tube]));
        }

        return $this->pheanstalk->statsTube($tube);
    }

    /**
     * {@inheritDoc}
     */
    public function stats()
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS, new CommandEvent($this));
        }

        return $this->pheanstalk->stats();
    }

    /**
     * {@inheritDoc}
     */
    public function touch($job)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::TOUCH, new CommandEvent($this, ['job' => $job]));
        }

        $this->pheanstalk->touch($job);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function useTube($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::USE_TUBE, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->useTube($tube);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function watch($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::WATCH, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->watch($tube);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function watchOnly($tube)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::WATCH_ONLY, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->watchOnly($tube);

        return $this;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param EventDispatcherInterface $dispatch
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
    public function setPheanstalk(PheanstalkInterface $pheanstalk)
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
