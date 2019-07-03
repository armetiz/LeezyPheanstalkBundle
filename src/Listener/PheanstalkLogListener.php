<?php

namespace Leezy\PheanstalkBundle\Listener;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PheanstalkLogListener implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CommandEvent::BURY               => 'onCommand',
            CommandEvent::DELETE             => 'onCommand',
            CommandEvent::IGNORE             => 'onCommand',
            CommandEvent::KICK               => 'onCommand',
            CommandEvent::LIST_TUBE_USED     => 'onCommand',
            CommandEvent::PAUSE_TUBE         => 'onCommand',
            CommandEvent::PEEK               => 'onCommand',
            CommandEvent::PEEK_READY         => 'onCommand',
            CommandEvent::PEEK_DELAYED       => 'onCommand',
            CommandEvent::PEEK_BURIED        => 'onCommand',
            CommandEvent::PUT                => 'onCommand',
            CommandEvent::PUT_IN_TUBE        => 'onCommand',
            CommandEvent::RELEASE            => 'onCommand',
            CommandEvent::RESERVE            => 'onCommand',
            CommandEvent::RESERVE_FROM_TUBE  => 'onCommand',
            CommandEvent::STATS              => 'onCommand',
            CommandEvent::STATS_TUBE         => 'onCommand',
            CommandEvent::STATS_JOB          => 'onCommand',
            CommandEvent::TOUCH              => 'onCommand',
            CommandEvent::USE_TUBE           => 'onCommand',
            CommandEvent::WATCH              => 'onCommand',
            CommandEvent::WATCH_ONLY         => 'onCommand',
        ];
    }

    public function onCommand(CommandEvent $event, string $eventName): void
    {
        if (!$this->logger) {
            return;
        }

        $pheanstalk = $event->getPheanstalk();

        $pheanstalkName = 'unknown';
        if ($pheanstalk instanceof PheanstalkProxyInterface) {
            $pheanstalkName = $pheanstalk->getName();
        }

        $nameExploded = explode('.', $eventName);

        $this->logger->info(
            'Pheanstalk command: '.$nameExploded[count($nameExploded) - 1],
            [
                'payload'    => $event->getPayload(),
                'pheanstalk' => $pheanstalkName,
            ]
        );
    }
}
