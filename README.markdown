## LeezyPheanstalkBundle

The LeezyPheanstalkBundle is a Symfony2 integration for [pheanstalk](https://github.com/pda/pheanstalk).
It provides a command line interface for manage the Beanstalkd server & a simple pheanstalk integration for use in your Symfony 2 application.

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

### List available tubes.

``` bash
$ php app/console leezy:pheanstalk:list-tube
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
    version=v1.1.0
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

### Step 2: Configure the Autoloader

Add the `Leezy` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Leezy' => __DIR__.'/../vendor/bundles',
));
```

Add the `Pheanstalk` prefixe to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerPrefixes(array(
    // ...
    'Pheanstalk'       => __DIR__.'/../vendor/pheanstalk/classes',
));
```


### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

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

``` yaml
# app/config/security.yml
leezy_pheanstalk:
    server: beanstalkd.domain.tld
    port: 11300
    timeout: 60
```