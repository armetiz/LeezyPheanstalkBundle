## Events

On each pheanstalk command, an event is dispatched.

See events name above :
* CommandEvent::BURY 
* CommandEvent::DELETE 
* CommandEvent::IGNORE 
* CommandEvent::KICK 
* CommandEvent::KICK_JOB
* CommandEvent::LIST_TUBE_USED
* CommandEvent::LIST_TUBES 
* CommandEvent::LIST_TUBES_WATCHED 
* CommandEvent::PAUSE_TUBE 
* CommandEvent::RESUME_TUBE
* CommandEvent::PEEK
* CommandEvent::PEEK_READY 
* CommandEvent::PEEK_DELAYED 
* CommandEvent::PEEK_BURIED 
* CommandEvent::PUT 
* CommandEvent::RELEASE
* CommandEvent::RESERVE 
* CommandEvent::RESERVE_WITH_TIMEOUT
* CommandEvent::STATS 
* CommandEvent::STATS_TUBE 
* CommandEvent::STATS_JOB 
* CommandEvent::TOUCH 
* CommandEvent::USE_TUBE 
* CommandEvent::WATCH 
* CommandEvent::WATCH_ONLY 

**Note** FQDN is `\Leezy\PheanstalkBundle\Event\CommandEvent`
**Note** If you need more documentation about those events; you should read the [beanstalkd protocol](https://raw.githubusercontent.com/kr/beanstalkd/master/doc/protocol.txt).

## Usage example

``` php
<?php

namespace Acme\DemoBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Leezy\PheanstalkBundle\Event\CommandEvent;

class PheanstalkSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents()
    {
        return array(
            CommandEvent::DELETE => array('onDelete', 0),
            CommandEvent::PUT => array('onPut', 0),
        );
    }

    public function onDelete(CommandEvent $event)
    {
        // ...
    }

    public function onPut(CommandEvent $event)
    {
        $pheanstalk = $event->getPheanstalk();

        $payload = $event->getPayload();
        $payload['data'];
        $payload['priority'];
        $payload['delay'];
        $payload['ttr'];
        // ...
    }

}
?>
```

``` php
<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {

    public function indexAction() {
        // ----------------------------------------
        // producer (queues jobs)

        $pheanstalk = $this->get("leezy.pheanstalk");
        $pheanstalk->useTube('testtube');
        $pheanstalk->put("job payload goes here\n");
    }

}
?>
```
