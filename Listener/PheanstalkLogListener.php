<?php

namespace Leezy\PheanstalkBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;

class PheanstalkLogListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    public static function getSubscribedEvents()
    {
        $listenEvents = array(
            CommandEvent::BURY => 'onCommand',
            CommandEvent::DELETE => 'onCommand',
            CommandEvent::IGNORE => 'onCommand',
            CommandEvent::KICK => 'onCommand',
            CommandEvent::LIST_TUBE_USED => 'onCommand',
            CommandEvent::LIST_TUBES => 'onCommand',
            CommandEvent::LIST_TUBES_WATCHED => 'onCommand',
            CommandEvent::PAUSE_TUBE => 'onCommand',
            CommandEvent::PEEK => 'onCommand',
            CommandEvent::PEEK_READY => 'onCommand',
            CommandEvent::PEEK_DELAYED => 'onCommand',
            CommandEvent::PEEK_BURIED => 'onCommand',
            CommandEvent::PUT => 'onCommand',
            CommandEvent::PUT_IN_TUBE => 'onCommand',
            CommandEvent::RELEASE => 'onCommand',
            CommandEvent::RESERVE => 'onCommand',
            CommandEvent::RESERVE_FROM_TUBE => 'onCommand',
            CommandEvent::STATS => 'onCommand',
            CommandEvent::STATS_TUBE => 'onCommand',
            CommandEvent::STATS_JOB => 'onCommand',
            CommandEvent::TOUCH => 'onCommand',
            CommandEvent::USE_TUBE => 'onCommand',
            CommandEvent::WATCH => 'onCommand',
            CommandEvent::WATCH_ONLY => 'onCommand',
        );

        return $listenEvents;
    }

    /**
     *
     * @param \Leezy\PheanstalkBundle\Event\CommandEvent $eventArgs
     */
    public function onCommand(CommandEvent $event)
    {
        if (!$this->getLogger()) {
            return;
        }

        $pheanstalk = $event->getPheanstalk();
        $connection = $pheanstalk->getConnection();

        if (!$connection->isServiceListening()) {
            $this->getLogger()->warning('Pheanstalk connection isn\'t linstening');
        }

        $pheanstalkName = 'unknown';
        if ($pheanstalk instanceof PheanstalkProxyInterface) {
            $pheanstalkName = $pheanstalk->getName();
        }

        $nameExploded = explode(".", $event->getName());

        $this->getLogger()->info('Pheanstalk command: ' . $nameExploded[count($nameExploded) - 1], array (
            'payload' => $event->getPayload(),
            'pheanstalk' => $pheanstalkName,
        ));
    }

    /**
     * @return \Symfony\Bridge\Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}
