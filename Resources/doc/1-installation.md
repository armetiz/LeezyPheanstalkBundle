## Installation

Installation is a quick 3 step process:

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

**Note:**
This bundle have a dependency to [pda/pheanstalk](https://github.com/pda/pheanstalk).
Beware that [pda/pheanstalk](https://github.com/pda/pheanstalk) is only available through composer as an unstable version.
The minimum-stability property of composer could lead to some problem. See this [issue](https://github.com/armetiz/LeezyPheanstalkBundle/issues/8#issuecomment-13480695)


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
    pheanstalks:
        primary:
            server: beanstalkd.domain.tld
            default: true
```
