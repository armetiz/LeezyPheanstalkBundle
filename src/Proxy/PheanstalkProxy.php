<?php

namespace Leezy\PheanstalkBundle\Proxy;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Contract\ResponseInterface;
use Pheanstalk\Job;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

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

    private function dispatch(string $command, array $payload = []): void
    {
        if (null === $this->dispatcher) {
            return;
        }

        if(version_compare(Kernel::VERSION, '4.3.0', '>=')) {
            $this->dispatcher->dispatch(new CommandEvent($this, $payload), $command);
        }
        else {
            $this->dispatcher->dispatch($command, new CommandEvent($this, $payload));
        }
    }

    public function bury(JobIdInterface $job, int $priority = PheanstalkInterface::DEFAULT_PRIORITY): void
    {
        $this->dispatch(CommandEvent::BURY, ['job' => $job, 'priority' => $priority]);

        $this->pheanstalk->bury($job, $priority);
    }

    public function delete(JobIdInterface $job): void
    {
        $this->dispatch(CommandEvent::DELETE, ['job' => $job]);

        $this->pheanstalk->delete($job);
    }

    public function ignore(string $tube): PheanstalkInterface
    {
        $this->dispatch(CommandEvent::IGNORE, ['tube' => $tube]);

        $this->pheanstalk->ignore($tube);

        return $this;
    }

    public function kick(int $max): int
    {
        $this->dispatch(CommandEvent::KICK, ['max' => $max]);

        return $this->pheanstalk->kick($max);
    }

    public function kickJob(JobIdInterface $job): void
    {
        $this->dispatch(CommandEvent::KICK_JOB, ['job' => $job]);

        $this->pheanstalk->kickJob($job);
    }

    public function listTubes(): array
    {
        $this->dispatch(CommandEvent::LIST_TUBES);

        return $this->pheanstalk->listTubes();
    }

    public function listTubesWatched(bool $askServer = false): array
    {
        $this->dispatch(CommandEvent::LIST_TUBES_WATCHED, ['askServer' => $askServer]);

        return $this->pheanstalk->listTubesWatched($askServer);
    }

    public function listTubeUsed(bool $askServer = false): string
    {
        $this->dispatch(CommandEvent::LIST_TUBE_USED, ['askServer' => $askServer]);

        return $this->pheanstalk->listTubeUsed($askServer);
    }

    public function pauseTube(string $tube, int $delay): void
    {
        $this->dispatch(CommandEvent::PAUSE_TUBE, ['tube' => $tube, 'delay' => $delay]);

        $this->pheanstalk->pauseTube($tube, $delay);
    }

    public function resumeTube(string $tube): void
    {
        $this->dispatch(CommandEvent::RESUME_TUBE, ['tube' => $tube]);

        $this->pheanstalk->resumeTube($tube);
    }

    public function peek(JobIdInterface $jobId): Job
    {
        $this->dispatch(CommandEvent::PEEK, ['jobId' => $jobId->getId()]);

        return $this->pheanstalk->peek($jobId);
    }

    public function peekReady(): ?Job
    {
        $this->dispatch(CommandEvent::PEEK_READY);

        return $this->pheanstalk->peekReady();
    }

    public function peekDelayed(): ?Job
    {
        $this->dispatch(CommandEvent::PEEK_DELAYED);

        return $this->pheanstalk->peekDelayed();
    }

    public function peekBuried(): ?Job
    {
        $this->dispatch(CommandEvent::PEEK_BURIED);

        return $this->pheanstalk->peekBuried();
    }

    public function put(
        string $data,
        int $priority = self::DEFAULT_PRIORITY,
        int $delay = self::DEFAULT_DELAY,
        int $ttr = self::DEFAULT_TTR
    ): Job {
        $this->dispatch(
            CommandEvent::PUT,
            [
                'data'     => $data,
                'priority' => $priority,
                'delay'    => $delay,
                'ttr'      => $ttr,
            ]
        );

        return $this->pheanstalk->put($data, $priority, $delay, $ttr);
    }

    public function release(
        JobIdInterface $job,
        int $priority = PheanstalkInterface::DEFAULT_PRIORITY,
        int $delay = PheanstalkInterface::DEFAULT_DELAY
    ): void {
        $this->dispatch(CommandEvent::RELEASE, ['job' => $job, 'priority' => $priority, 'delay' => $delay]);

        $this->pheanstalk->release($job, $priority, $delay);
    }

    public function reserve(): ?Job
    {
        $this->dispatch(CommandEvent::RESERVE);

        return $this->pheanstalk->reserve();
    }

    public function reserveWithTimeout(int $timeout): ?Job
    {
        $this->dispatch(CommandEvent::RESERVE_WITH_TIMEOUT);

        return $this->pheanstalk->reserveWithTimeout($timeout);
    }


    public function statsJob(JobIdInterface $job): ResponseInterface
    {
        $this->dispatch(CommandEvent::STATS_JOB, ['job' => $job]);

        return $this->pheanstalk->statsJob($job);
    }

    public function statsTube(string $tube): ResponseInterface
    {
        $this->dispatch(CommandEvent::STATS_TUBE, ['tube' => $tube]);

        return $this->pheanstalk->statsTube($tube);
    }

    public function stats(): ResponseInterface
    {
        $this->dispatch(CommandEvent::STATS);

        return $this->pheanstalk->stats();
    }

    public function touch(JobIdInterface $job): void
    {
        $this->dispatch(CommandEvent::TOUCH, ['job' => $job]);

        $this->pheanstalk->touch($job);
    }

    public function useTube(string $tube): PheanstalkInterface
    {
        $this->dispatch(CommandEvent::USE_TUBE, ['tube' => $tube]);

        $this->pheanstalk->useTube($tube);

        return $this;
    }

    public function watch(string $tube): PheanstalkInterface
    {
        $this->dispatch(CommandEvent::WATCH, ['tube' => $tube]);

        $this->pheanstalk->watch($tube);

        return $this;
    }

    public function watchOnly(string $tube): PheanstalkInterface
    {
        $this->dispatch(CommandEvent::WATCH_ONLY, ['tube' => $tube]);

        $this->pheanstalk->watchOnly($tube);

        return $this;
    }

    public function setDispatcher(EventDispatcherInterface $dispatch): void
    {
        $this->dispatcher = $dispatch;
    }

    public function getPheanstalk(): PheanstalkInterface
    {
        return $this->pheanstalk;
    }
}
