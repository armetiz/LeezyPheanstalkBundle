<?php

namespace Leezy\PheanstalkBundle\Listener;

use Leezy\PheanstalkBundle\Event\CommandEvent;

class PheanstalkListener implements EventSubscriber
{
    protected $logger;
    
    protected $listenEvents = array(
        CommandEvent::BURY,
        CommandEvent::DELETE,
        CommandEvent::IGNORE,
        CommandEvent::KICK,
        CommandEvent::LIST_TUBES,
        CommandEvent::LIST_TUBES_WATCHED,
        CommandEvent::LIST_TUBE_USED,
        CommandEvent::PEEK,
        CommandEvent::PEEK_BURIED,
        CommandEvent::PEEK_DELAYED,
        CommandEvent::PEEK_READY,
        CommandEvent::PUT,
        CommandEvent::PUT_IN_TUBE,
        CommandEvent::RELEASE,
        CommandEvent::RESERVE,
        CommandEvent::STATS,
        CommandEvent::STATS_JOB,
        CommandEvent::STATS_TUBE,
        CommandEvent::TOUCH,
        CommandEvent::USE_TUBE,
        CommandEvent::WATCH,
        CommandEvent::WATCH_ONLY,
    );

    public function getSubscribedEvents()
    {
        return $this->listenEvents;
    }
    
    /**
     * 
     * @param \Leezy\PheanstalkBundle\Event\CommandEvent $eventArgs
     */
    protected function wildcardListener(CommandEvent $event)
    {
        $pheanstalk = $event->getPheanstalk();
        $connection = $pheanstalk->getConnection();
        
        if(!$connection->isServiceListening()) {
            $this->getLogger()->warn();
        }
        
        $nameExploded = explode(".", $event->getName());
        
        $this->getLogger()->log('Command: ' . $nameExploded[count($nameExploded) - 1]);
    }
    
    public function __call($name, $arguments)
    {
        if(in_array($name, $this->listenEvents)) {
            call_user_func_array(array($this, 'wildcardListener'), $arguments);
        }
    }
    
    public function getLogger()
    {
        
    }
    
    public function setLogger($logger)
    {
        
    }
}
