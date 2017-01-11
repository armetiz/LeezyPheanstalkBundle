## LeezyPheanstalkBundle

[![Build Status](https://secure.travis-ci.org/armetiz/LeezyPheanstalkBundle.png)](http://travis-ci.org/armetiz/LeezyPheanstalkBundle)
[![Packagist](https://poser.pugx.org/leezy/pheanstalk-bundle/downloads.png)](https://packagist.org/packages/leezy/pheanstalk-bundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/armetiz/LeezyPheanstalkBundle/badges/quality-score.png?s=7580874ae743d4ee26a0402b3af68fb9277972b8)](https://scrutinizer-ci.com/g/armetiz/LeezyPheanstalkBundle/)

[Beanstalkd workqueue](http://kr.github.com/beanstalkd/) clients for Symfony2.

The LeezyPheanstalkBundle is a Symfony2 Bundle that provides a [pheanstalk](https://github.com/pda/pheanstalk) integration with the following features:
* Command Line Interface for manage the queues.
* An integration to the Symfony2 event system.
* An integration to the Symfony2 profiler system to monitor your beanstalk server.
* An integration to the Symfony2 logger system.
* A proxy system to customize the command features.


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
* [Peter Kruithof](https://github.com/pkruithof) : Version 3
* [Maxwell2022](https://github.com/Maxwell2022) : Symfony2 Profiler integration
