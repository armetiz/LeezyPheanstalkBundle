## Custom proxy

Add a custom proxy only if you can't do what you want using [Events](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/Resources/doc/4-events.md) hook system.

# Create a proxy class

Two choices: 
* Implement **Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface**
* Extend **Leezy\PheanstalkBundle\Proxy\PheanstalkProxy**

```php
<?php

namespace Acme\DemoBundle\Proxy;

use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy as PheanstalkProxyBase;

class PheanstalkProxy extends PheanstalkProxyBase {
    /**
     * {@inheritDoc}
     */
    public function bury($job, $priority = Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY)
    {
        //crazy job here

        return parent::bury($job, $priority);
    }
}
?>
```

# Define proxy class on the container

The injection of a dispatcher isn't mandatory. Don't inject it and the logger will be disabled.

```xml
<service id="acme.demo.pheanstalk.proxy" class="Acme\DemoBundle\Proxy\PheanstalkProxy">
    <call method="setDispatcher">
        <argument type="service" id="event_dispatcher" on-invalid="ignore"/>
    </call>
</service>
```

# Configure pheanstalk_bundle

``` yaml
# app/config/config.yml
leezy_pheanstalk:
    pheanstalks:
        foo_bar:
            server: beanstalkd-2.domain.tld
            default: true
            proxy: acme.demo.pheanstalk.proxy
```
