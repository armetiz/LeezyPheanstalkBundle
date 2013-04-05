== 2.1.2 ==
* Add KickJobCommand
* Fix error management on command

== 2.1.1 ==
* Fix missing implementation of PheanstalkInterface::kickJob

== 2.1.0 ==
* Use pda/pheanstalk 2.1.0
* Add KickCommand
* Fix default name on Command

== 2.0.1 ==
* Set type hint for logger
* Add few tests on PheanstalkLogListener

== 2.0.0 ==
* Add PheanstalkProxy
* Allow user to define custom PheanstalkProxy
* Fragment documentation
* Events documentation
* Change 'connection' name to 'pheanstalk'
* Event integration
* BC : Use 'pheanstalk' name instead of 'connection' to avoid confusion
* Add more security on CLI
* Logger integration
* Add documentation on custom proxy

== 1.2.0 ==
* Add profilter integration, thanks @Maxwell2022
* Add ConnectionLocator

== 1.1.0 ==
* Remove useless Tests/Fixtures/Entity/FakeMedia.php
* Update composer to use symfony/framework-bundle instead of symfony/symfony
* Remove requirement PHP >= 5.3.3. Use the PHP requirement of symfony/framework-bundle
* Fix Travis-CI problem

== 1.0.4 ==
* Update to PSR-0 : https://github.com/pda/pheanstalk/commit/412ab5b7469538ab613efd8fb91b4adc84bf599f
* Add warning about composer minimum-stability due to : https://github.com/armetiz/LeezyPheanstalkBundle/issues/8

== 1.0.3 ==
* Update code to pda/pheanstalk which is a PHP5.2 implementation.
* BC still ok.

== 1.0.2 ==
* Add Unit Test
* Throw exception when more than one connection have the default flag
* Switch dependency from 'mrpoundsign/pheanstalk-5.3' to 'pda/pheanstalk'
* Add TravisCI configuration

== 1.0.1 ==
* Fix dependency to pheanstalk-5.3. #4

== 1.0.0 ==
* Update README to be composer compliant.
* First stable tag version.