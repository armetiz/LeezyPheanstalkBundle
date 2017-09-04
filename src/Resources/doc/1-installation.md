## Installation

Installation is a quick 3 step process:

1. Download LeezyPheanstalkBundle using composer
2. Enable the Bundle
3. Configure your application's config.yml

### Step 1: Require LeezyPheanstalkBundle

Tell composer to require this bundle by running:

``` bash
$ composer require leezy/pheanstalk-bundle
```

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
    pheanstalks:
        primary:
            server: beanstalkd.domain.tld
            default: true
```
