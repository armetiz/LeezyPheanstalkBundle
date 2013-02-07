## LeezyPheanstalkBundle

[![project status](http://stillmaintained.com/armetiz/LeezyPheanstalkBundle.png)](http://stillmaintained.com/armetiz/LeezyPheanstalkBundle)
[![Build Status](https://secure.travis-ci.org/armetiz/LeezyPheanstalkBundle.png)](http://travis-ci.org/armetiz/LeezyPheanstalkBundle)

The LeezyPheanstalkBundle is a Symfony2 Bundle that provides a command line interface 
for manage the [Beanstalkd workqueue](http://kr.github.com/beanstalkd/) server & 
a [pheanstalk](https://github.com/pda/pheanstalk) integration.

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

## LeezyPheanstalkBundle Command Line Tools

The LeezyPheanstalkBundle provides a number of command line utilities. 
Commands are available for the following tasks:

1. Delete a job.
2. Flush a tube.
3. List available tubes.
4. Pause a tube.
5. Peek a job and get associated data.
6. Put a new job in a tube.
7. Get statistics about beanstalkd server.
8. Get statistics about a job.
9. Get statistics about a tube.

**Note:**

```
You must have correctly installed and configured the LeezyPheanstalkBundle before using 
these commands.
```

### Delete a job

``` bash
$ php app/console leezy:pheanstalk:delete-job 42
```

### Flush a tube.

``` bash
$ php app/console leezy:pheanstalk:flush-tube your-tube
```

**Note:**

```
When you flush a tube, it will be removed from the beanstalkd server.
```

### List available tubes.

``` bash
$ php app/console leezy:pheanstalk:list-tube
```

**Note:**

```
Tubes that are display contains at least one job.
```

### Pause a tube.

``` bash
$ php app/console leezy:pheanstalk:pause-tube your-tube
```

### Peek a job and get associated data.

``` bash
$ php app/console leezy:pheanstalk:peek 42
```

### Put a new job in a tube.

``` bash
$ php app/console leezy:pheanstalk:put your-tube "Hello world - I am a job"
```

### Get statistics about beanstalkd server.

``` bash
$ php app/console leezy:pheanstalk:stats
```

### Get statistics about a job.

``` bash
$ php app/console leezy:pheanstalk:stats-job 42
```

### Get statistics about a tube.

``` bash
$ php app/console leezy:pheanstalk:stats-tube your-tube
```

## Installation

Installation is a quick 4 step process:

1. Download LeezyPheanstalkBundle using composer
2. Enable the Bundle
3. Configure your application's config.yml

### Step 1: Download LeezyPheanstalkBundle

Add LeezyPheanstalkBundle in your composer.json:

```js
{
    "require": {
        "leezy/pheanstalk-bundle": "1.*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update leezy/pheanstalk-bundle
```

Composer will install the bundle to your project's `vendor/leezy` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Leezy\PheanstalkBundle\LeezyPheanstalkBundle(),
    );
}
```

### Step 3: Configure your application's config.yml

Finally, add the following to your config.yml

``` yaml
# app/config/config.yml
leezy_pheanstalk:
    enabled: true
    connection:
        primary:
            server: beanstalkd.domain.tld
            port: 11300
            timeout: 60
        secondary:
            server: beanstalkd-2.domain.tld
            default: true
```

## Configuration
This bundle can be configured, and this is the list of what you can do :
- Create many connection. Note that each connection is a Pheanstalk_Pheanstalk instance.
- Define specific server / host for each connection.
- Define specific port for each connection. This option is optional and default value is 11300.
- Define specific timeout for each connection. Timeout refere to the connection timeout. This option is optional and default value is 60.
- Disable this bundle. This options is optional and default value is true. 

**Note:**

```
You can retreive each connection using the container with "leezy.pheanstalk.[connection_name]".

When you define a "default" connection. You can have a direct access to it with "leezy.pheanstalk".
```

``` php
<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {

    public function indexAction() {
        $pheanstalkPrimary = $this->get("leezy.pheanstalk.primary");
        $pheanstalkSecondary = $this->get("leezy.pheanstalk");

        // ----------------------------------------
        // producer (queues jobs) on beanstalk.domain.tld

        $pheanstalkDefault
          ->useTube('testtube')
          ->put("job payload goes here\n");

        // ----------------------------------------
        // worker (performs jobs) on beanstalk-2.domain.tld

        $job = $pheanstalkSecondary
          ->watch('testtube')
          ->ignore('default')
          ->reserve();

        echo $job->getData();

        $pheanstalkSecondary->delete($job);
    }

}
?>
```