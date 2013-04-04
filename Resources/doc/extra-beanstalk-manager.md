## Extra Part - A simple Beanstalkd Manager

This is an extra part about how to use PheanstalkBundle CLI really quickly.

1. Create a "pheanstalkd-manager" folder in your $HOME
2. Get Symfony Standard-Edition
3. Download composer
4. Download Symfony dependencies
5. Add & Download LeezyPheanstalkBundle on your Symfony application
6. Configure LeezyPheanstalkBundle

**Note:**

```
Be aware of that. To keep simple, this part is dealing only with unstable version.
```


``` bash
$ cd ~ && mkdir pheanstalkd-manager && cd pheanstalkd-manager    
$ git clone git://github.com/symfony/symfony-standard.git .
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
$ php composer.phar require leezy/pheanstalk-bundle
$ # Please provide a version constraint [...]: 1.x-dev
```

For the last operation, take a look on [Installation](https://github.com/armetiz/LeezyPheanstalkBundle/tree/2.1.0/Resources/doc/1-installation.md) about how to enable and configure the bundle.
