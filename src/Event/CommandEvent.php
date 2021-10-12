<?php

namespace Leezy\PheanstalkBundle\Event;

use Pheanstalk\Contract\PheanstalkInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommandEvent extends Event
{
    public const BURY = 'leezy.pheanstalk.event.bury';
    public const DELETE = 'leezy.pheanstalk.event.delete';
    public const IGNORE = 'leezy.pheanstalk.event.ignore';
    public const KICK = 'leezy.pheanstalk.event.kick';
    public const KICK_JOB = 'leezy.pheanstalk.event.kick_job';
    public const LIST_TUBE_USED = 'leezy.pheanstalk.event.list_tube_used';
    public const LIST_TUBES = 'leezy.pheanstalk.event.list_tubes';
    public const LIST_TUBES_WATCHED = 'leezy.pheanstalk.event.list_tubes_watched';
    public const PAUSE_TUBE = 'leezy.pheanstalk.event.pause_tube';
    public const RESUME_TUBE = 'leezy.pheanstalk.event.resume_tube';
    public const PEEK = 'leezy.pheanstalk.event.peek';
    public const PEEK_READY = 'leezy.pheanstalk.event.peek_ready';
    public const PEEK_DELAYED = 'leezy.pheanstalk.event.peek_delayed';
    public const PEEK_BURIED = 'leezy.pheanstalk.event.peek_buried';
    public const PUT = 'leezy.pheanstalk.event.put';
    public const RELEASE = 'leezy.pheanstalk.event.release';
    public const RESERVE = 'leezy.pheanstalk.event.reserve';
    public const RESERVE_WITH_TIMEOUT = 'leezy.pheanstalk.event.reserve_with_timeout';
    public const STATS = 'leezy.pheanstalk.event.stats';
    public const STATS_TUBE = 'leezy.pheanstalk.event.stats_tube';
    public const STATS_JOB = 'leezy.pheanstalk.event.stats_job';
    public const TOUCH = 'leezy.pheanstalk.event.touch';
    public const USE_TUBE = 'leezy.pheanstalk.event.use_tube';
    public const WATCH = 'leezy.pheanstalk.event.watch';
    public const WATCH_ONLY = 'leezy.pheanstalk.event.watch_only';

    /**
     * @var PheanstalkInterface
     */
    private $pheanstalk;

    /**
     * @var array
     */
    private $payload;

    public function __construct(PheanstalkInterface $pheanstalk, array $payload = [])
    {
        $this->pheanstalk = $pheanstalk;
        $this->payload = $payload;
    }

    public function getPheanstalk(): PheanstalkInterface
    {
        return $this->pheanstalk;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public static function availableEvents(): array
    {
        return [
            self::BURY,
            self::DELETE,
            self::IGNORE,
            self::KICK,
            self::KICK_JOB,
            self::LIST_TUBE_USED,
            self::LIST_TUBES,
            self::LIST_TUBES_WATCHED,
            self::PAUSE_TUBE,
            self::RESUME_TUBE,
            self::PEEK,
            self::PEEK_READY,
            self::PEEK_DELAYED,
            self::PEEK_BURIED,
            self::PUT,
            self::RELEASE,
            self::RESERVE,
            self::RESERVE_WITH_TIMEOUT,
            self::STATS,
            self::STATS_TUBE,
            self::STATS_JOB,
            self::TOUCH,
            self::USE_TUBE,
            self::WATCH,
            self::WATCH_ONLY,
        ];
    }
}
