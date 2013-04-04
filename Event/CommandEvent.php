<?php

namespace Leezy\PheanstalkBundle\Event;

use Symfony\Component\EventDispatcher\Event as EventBase;

use Pheanstalk_PheanstalkInterface;

class CommandEvent extends EventBase
{
    const BURY = "leezy.pheanstalk.event.bury";
    const DELETE = "leezy.pheanstalk.event.delete";
    const IGNORE = "leezy.pheanstalk.event.ignore";
    const KICK = "leezy.pheanstalk.event.kick";
    const KICK_JOB = "leezy.pheanstalk.event.kick_job";
    const LIST_TUBE_USED = "leezy.pheanstalk.event.list_tube_used";
    const LIST_TUBES = "leezy.pheanstalk.event.list_tubes";
    const LIST_TUBES_WATCHED = "leezy.pheanstalk.event.list_tubes_watched";
    const PAUSE_TUBE = "leezy.pheanstalk.event.pause_tube";
    const PEEK = "leezy.pheanstalk.event.peek";
    const PEEK_READY = "leezy.pheanstalk.event.peek_ready";
    const PEEK_DELAYED = "leezy.pheanstalk.event.peek_delayed";
    const PEEK_BURIED = "leezy.pheanstalk.event.peek_buried";
    const PUT = "leezy.pheanstalk.event.put";
    const PUT_IN_TUBE = "leezy.pheanstalk.event.put_in_tube";
    const RELEASE = "leezy.pheanstalk.event.release";
    const RESERVE = "leezy.pheanstalk.event.reserve";
    const RESERVE_FROM_TUBE = "leezy.pheanstalk.event.reserve_from_tube";
    const STATS = "leezy.pheanstalk.event.stats";
    const STATS_TUBE = "leezy.pheanstalk.event.stats_tube";
    const STATS_JOB = "leezy.pheanstalk.event.stats_job";
    const TOUCH = "leezy.pheanstalk.event.touch";
    const USE_TUBE = "leezy.pheanstalk.event.use_tube";
    const WATCH = "leezy.pheanstalk.event.watch";
    const WATCH_ONLY = "leezy.pheanstalk.event.watch_only";

    private $pheanstalk;
    private $payload;

    public function __construct(Pheanstalk_PheanstalkInterface $pheanstalk, array $payload = array())
    {
        $this->pheanstalk = $pheanstalk;
        $this->payload = $payload;
    }

    /**
     * @return \Leezy\PheanstalkBundle\Proxy\PheanstalkInterface
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
