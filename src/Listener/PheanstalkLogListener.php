<?php

namespace Leezy\PheanstalkBundle\Listener;

use Leezy\PheanstalkBundle\Event\CommandEvent;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PheanstalkLogListener implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    /** @var PheanstalkLocator */
    private $locator;

    public function __construct(PheanstalkLocator $locator)
    {
        $this->locator = $locator;
    }


    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return array_fill_keys(CommandEvent::availableEvents(), 'onCommand');
    }

    public function onCommand(CommandEvent $event, string $eventName): void
    {
        if (!$this->logger) {
            return;
        }

        $nameExploded = explode('.', $eventName);

        $this->logger->info(
            'Pheanstalk command: '.$nameExploded[count($nameExploded) - 1],
            [
                'payload' => $event->getPayload(),
                'name' => $this->locator->getPheanstalkName($event->getPheanstalk())
            ]
        );
    }
}
