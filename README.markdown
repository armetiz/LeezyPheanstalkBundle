## LeezyPheanstalkBundle

The LeezyPheanstalkBundle is a Symfony2 integration for [pheanstalk](https://github.com/pda/pheanstalk)
It provides a command line interface for manage the Beanstalkd server.

Features include:

- Delete a job.
- Flush a tube.
- List available tubes.
- Pause a tube.
- Peek a job and get associated datas.
- Put a new job in a tube.
- Get statistics about beanstalkd server.
- Get statistics about a job.
- Get statistics about a tube.

How to
-------------

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