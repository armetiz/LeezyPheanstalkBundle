<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Contract\ResponseInterface;
use Pheanstalk\Job;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PheanstalkProxy implements PheanstalkProxyInterface
{
    /** @var PheanstalkInterface */
    protected $pheanstalk;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    public function __construct(PheanstalkInterface $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    public function bury(JobIdInterface $job, int $priority = PheanstalkInterface::DEFAULT_PRIORITY): void
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::BURY,
                new CommandEvent($this, ['job' => $job, 'priority' => $priority]));
        }

        $this->pheanstalk->bury($job, $priority);
    }

    public function delete(JobIdInterface $job): void
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::DELETE, new CommandEvent($this, ['job' => $job]));
        }

        $this->pheanstalk->delete($job);
    }

    public function ignore(string $tube): PheanstalkInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::IGNORE, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->ignore($tube);

        return $this;
    }

    public function kick(int $max): int
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::KICK, new CommandEvent($this, ['max' => $max]));
        }

        return $this->pheanstalk->kick($max);
    }

    public function kickJob(JobIdInterface $job): void
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::KICK_JOB, new CommandEvent($this, ['job' => $job]));
        }

        $this->pheanstalk->kickJob($job);
    }

    public function listTubes(): array
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBES, new CommandEvent($this));
        }

        return $this->pheanstalk->listTubes();
    }

    public function listTubesWatched(bool $askServer = false): array
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBES_WATCHED,
                new CommandEvent($this, ['askServer' => $askServer]));
        }

        return $this->pheanstalk->listTubesWatched($askServer);
    }

    public function listTubeUsed(bool $askServer = false): string
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::LIST_TUBE_USED,
                new CommandEvent($this, ['askServer' => $askServer]));
        }

        return $this->pheanstalk->listTubeUsed($askServer);
    }

    public function pauseTube(string $tube, int $delay): void
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PAUSE_TUBE,
                new CommandEvent($this, ['tube' => $tube, 'delay' => $delay]));
        }

        $this->pheanstalk->pauseTube($tube, $delay);
    }

    public function resumeTube(string $tube): void
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESUME_TUBE, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->resumeTube($tube);
    }

    public function peek(JobIdInterface $jobId): Job
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK, new CommandEvent($this, ['jobId' => $jobId->getId()]));
        }

        return $this->pheanstalk->peek($jobId);
    }

    public function peekReady(): ?Job
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_READY, new CommandEvent($this));
        }

        return $this->pheanstalk->peekReady();
    }

    public function peekDelayed(): ?Job
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_DELAYED, new CommandEvent($this));
        }

        return $this->pheanstalk->peekDelayed();
    }

    public function peekBuried(): ?Job
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::PEEK_BURIED, new CommandEvent($this));
        }

        return $this->pheanstalk->peekBuried();
    }

    public function put(
        string $data,
        int $priority = self::DEFAULT_PRIORITY,
        int $delay = self::DEFAULT_DELAY,
        int $ttr = self::DEFAULT_TTR
    ): Job {
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

    public function release(
        JobIdInterface $job,
        int $priority = PheanstalkInterface::DEFAULT_PRIORITY,
        int $delay = PheanstalkInterface::DEFAULT_DELAY
    ): void {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                CommandEvent::RELEASE,
                new CommandEvent($this, ['job' => $job, 'priority' => $priority, 'delay' => $delay])
            );
        }

        $this->pheanstalk->release($job, $priority, $delay);
    }

    public function reserve(): ?Job
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESERVE, new CommandEvent($this));
        }

        return $this->pheanstalk->reserve();
    }

    public function reserveWithTimeout(int $timeout): ?Job
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::RESERVE_WITH_TIMEOUT, new CommandEvent($this));
        }

        return $this->pheanstalk->reserveWithTimeout($timeout);
    }


    public function statsJob(JobIdInterface $job): ResponseInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS_JOB, new CommandEvent($this, ['job' => $job]));
        }

        return $this->pheanstalk->statsJob($job);
    }

    public function statsTube(string $tube): ResponseInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS_TUBE, new CommandEvent($this, ['tube' => $tube]));
        }

        return $this->pheanstalk->statsTube($tube);
    }

    public function stats(): ResponseInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::STATS, new CommandEvent($this));
        }

        return $this->pheanstalk->stats();
    }

    public function touch(JobIdInterface $job): void
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::TOUCH, new CommandEvent($this, ['job' => $job]));
        }

        $this->pheanstalk->touch($job);
    }

    public function useTube(string $tube): PheanstalkInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::USE_TUBE, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->useTube($tube);

        return $this;
    }

    public function watch(string $tube): PheanstalkInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::WATCH, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->watch($tube);

        return $this;
    }

    public function watchOnly(string $tube): PheanstalkInterface
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(CommandEvent::WATCH_ONLY, new CommandEvent($this, ['tube' => $tube]));
        }

        $this->pheanstalk->watchOnly($tube);

        return $this;
    }

    public function setDispatcher(EventDispatcherInterface $dispatch)
    {
        $this->dispatcher = $dispatch;
    }

    public function getPheanstalk(): PheanstalkInterface
    {
        return $this->pheanstalk;
    }
}
