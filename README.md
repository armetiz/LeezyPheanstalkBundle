## LeezyPheanstalkBundle

[![Build Status](https://travis-ci.org/armetiz/LeezyPheanstalkBundle.svg?branch=master)](http://travis-ci.org/armetiz/LeezyPheanstalkBundle)
[![Packagist](https://poser.pugx.org/leezy/pheanstalk-bundle/downloads.png)](https://packagist.org/packages/leezy/pheanstalk-bundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/armetiz/LeezyPheanstalkBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/armetiz/LeezyPheanstalkBundle/)

[Beanstalkd workqueue](http://kr.github.com/beanstalkd/) clients for Symfony.

The LeezyPheanstalkBundle is a Symfony Bundle that provides a [pheanstalk](https://github.com/pda/pheanstalk) integration with the following features:
* Command Line Interface for manage the queues.
* An integration to the Symfony event system.
* An integration to the Symfony profiler system to monitor your beanstalk server.
* An integration to the Symfony logger system.
* A proxy system to customize the command features.
* Auto-wiring: `PheanstalkInterface`

Support Symfony 2, 3, 4 and 5.


Documentation :
- [Installation](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/src/Resources/doc/1-installation.md)
- [Configuration](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/src/Resources/doc/2-configuration.md)
- [CLI Usage](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/src/Resources/doc/3-cli.md)
- [Events](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/src/Resources/doc/4-events.md)
- [Custom proxy](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/src/Resources/doc/5-custom-proxy.md)
- [Extra - Beanstalk Manager](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/src/Resources/doc/6-extra-beanstalk-manager.md)
- [Extra - Proxy to prefix tubes](https://github.com/h4cc/LeezyPheanstalkBundleExtra)

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

## Testing

```bash
$ php composer.phar update
$ phpunit
```

## License

This bundle is under the MIT license. [See the complete license](https://github.com/armetiz/LeezyPheanstalkBundle/blob/master/LICENSE).

## Other

[Silex integration](https://github.com/sergiors/pheanstalk-service-provider)

## Credits

Author - [Thomas Tourlourat](http://www.armetiz.info)

Contributor :
* [dontub](https://github.com/dontub) : Version 4
* [Peter Kruithof](https://github.com/pkruithof) : Version 3
* [Maxwell2022](https://github.com/Maxwell2022) : Symfony2 Profiler integration
