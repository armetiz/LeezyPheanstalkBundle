## LeezyPheanstalkBundle

**This branch is under development**.

[![project status](http://stillmaintained.com/armetiz/LeezyPheanstalkBundle.png)](http://stillmaintained.com/armetiz/LeezyPheanstalkBundle)
[![Build Status](https://secure.travis-ci.org/armetiz/LeezyPheanstalkBundle.png)](http://travis-ci.org/armetiz/LeezyPheanstalkBundle)

[Beanstalkd workqueue](http://kr.github.com/beanstalkd/) clients for Symfony2.

The LeezyPheanstalkBundle is a Symfony2 Bundle that provides a [pheanstalk](https://github.com/pda/pheanstalk) integration with the following features:
* Command Line Interface for manage the queues.
* An integration to the Symfony2 event system.
* An integration to the Symfony2 profiler system to monitor your beanstalk server.
* An integration to the Symfony2 logger system.
* A proxy system to customize the command features.


* [Installation](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/Resources/doc/1-installation.md)
* [Configuration](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/Resources/doc/2-configuration.md)
* [CLI Usage](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/Resources/doc/3-cli.md)
* [Events](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/Resources/doc/4-events.md)
* [Beanstalk Manager](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/Resources/doc/extra-beanstalk-manager.md)

## Usage example

```php
<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {

    public function indexAction() {
        $pheanstalk = $this->get("leezy.pheanstalk");

        // ----------------------------------------
        // producer (queues jobs)

        $pheanstalk
          ->useTube('testtube')
          ->put("job payload goes here\n");

        // ----------------------------------------
        // worker (performs jobs)

        $job = $pheanstalk
          ->watch('testtube')
          ->ignore('default')
          ->reserve();

        echo $job->getData();

        $pheanstalk->delete($job);
    }

}
?>
```
