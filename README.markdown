## LeezyPheanstalkBundle

The LeezyPheanstalkBundle is a Symfony2 integration for [pheanstalk](https://github.com/pda/pheanstalk).
It provides a command line interface for manage the Beanstalkd server & a pheanstalk integration for use in your Symfony 2 application.

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
When you flush a tube, it will be remove from the beanstalkd server.
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

1. Download LeezyPheanstalkBundle
2. Configure the Autoloader
3. Enable the Bundle
4. Configure your application's config.yml

### Step 1: Download LeezyPheanstalkBundle

Ultimately, the LeezyPheanstalkBundle files should be downloaded to the
`vendor/bundles/Leezy/PheanstalkBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony2 method.

**Using the vendors script**

Add the following lines in your `deps` file:

```
[LeezyPheanstalkBundle]
    git=git://github.com/pda/pheanstalk.git
    target=bundles/Leezy/PheanstalkBundle

[Pheanstalk]
    git=https://github.com/pda/pheanstalk.git
    target=/pheanstalk
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

### Step 2: Configure the Autoloader

Add the `Leezy` and `Pheanstalk` namespaces to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Pheanstalk'       => __DIR__.'/../vendor/pheanstalk/classes',
    'Leezy' => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Leezy\PheanstalkBundle\PheanstalkBundle(),
    );
}
```

### Step 4: Configure your application's config.yml

Finally, add the following to your config.yml

``` yaml
# app/config/config.yml
leezy_pheanstalk:
    enabled: true
    connection:
        default:
            server: beanstalkd.domain.tld
            port: 11300
            timeout: 60
        secondary:
            server: beanstalkd-2.domain.tld
            ignore_default: true
```

## Configuration
This bundle can be configured, and this is the list of what you can do :
- Create many connection. Note that each connection is a Pheanstalk instance.
- Define specific server / host for each connection.
- Define specific port for each connection. This option is optional and default value is 11300.
- Define specific timeout for each connection. Timeout refere to the connection timeout. This option is optional and default value is 60.
- Ignore the "default" tube. When testing or watch for example, many people ignored "default" tube for consumer. So, now it can be automatic. This option is optional and default value is false.
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
        $pheanstalkDefault = $this->get("leezy.pheanstalk");
        $pheanstalkSecondary = $this->get("leezy.pheanstalk.secondary");

        // ----------------------------------------
        // producer (queues jobs) on beanstalk.domain.tld

        $pheanstalkDefault
          ->useTube('testtube')
          ->put("job payload goes here\n");

        // ----------------------------------------
        // worker (performs jobs) on beanstalk-2.domain.tld

        $job = $pheanstalkSecondary
          ->watch('testtube')
          ->reserve();

        echo $job->getData();

        $pheanstalkSecondary->delete($job);
    }

}
?>
```