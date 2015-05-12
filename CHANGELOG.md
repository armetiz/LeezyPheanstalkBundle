## 3.0.0

This version does not break compatibility itself, but it does upgrade
Pheanstalk to version 3, which uses namespaces. If you use any Pheanstalk
classes directly in your code, you should update those references.

* Upped Pheanstalk version to ~3.0
* Upped Symfony requirement to ~2.3
* Upped PHP version requirement to 5.5.9
* Added PRS-4 autoloader configuration
* The option `enabled` was removed from configuration
* The `*.class` service parameters were replaced by its real values

## 2.5.0
* Logging is now done to a specific channel (PR #50 @pkruithof)

## 2.4.0
* Add CLI Command to display next ready job

## 2.3.0
* PR #43 - StatsTube shows stats for all tube if no argument is given

## 2.2.0
* Pull Request #41 Fix chainable methods to return proxy object

## 2.1.6
* Add command to view the first ready/burried job in a certrain tube
* Remove useless import
* Add missing use
* Add Scrutinizer Badge
* Add link to LeezyPheanstalkBundleExtra

## 2.1.5
* Fix #38

## 2.1.4
* Fix install documentation.
* Fix #29
* Remove github analytics
* Fixed FlushTube Command by catching the correct Exception
* Pretty smal fixes to be more PSR2 compliant
* Move connection definition from compiler pass to the extension

## 2.1.3
* Fixed return value for Proxy::putInTube
* CHANGELOG use markdown extension

## 2.1.2
* Add KickJobCommand
* Fix error management on command

## 2.1.1
* Fix missing implementation of PheanstalkInterface::kickJob

## 2.1.0
* Use pda/pheanstalk 2.1.0
* Add KickCommand
* Fix default name on Command

## 2.0.1
* Set type hint for logger
* Add few tests on PheanstalkLogListener

## 2.0.0
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

## 1.2.0
* Add profilter integration, thanks @Maxwell2022
* Add ConnectionLocator

## 1.1.0
* Remove useless Tests/Fixtures/Entity/FakeMedia.php
* Update composer to use symfony/framework-bundle instead of symfony/symfony
* Remove requirement PHP >= 5.3.3. Use the PHP requirement of symfony/framework-bundle
* Fix Travis-CI problem

## 1.0.4
* Update to PSR-0 : https://github.com/pda/pheanstalk/commit/412ab5b7469538ab613efd8fb91b4adc84bf599f
* Add warning about composer minimum-stability due to : https://github.com/armetiz/LeezyPheanstalkBundle/issues/8

## 1.0.3
* Update code to pda/pheanstalk which is a PHP5.2 implementation.
* BC still ok.

## 1.0.2
* Add Unit Test
* Throw exception when more than one connection have the default flag
* Switch dependency from 'mrpoundsign/pheanstalk-5.3' to 'pda/pheanstalk'
* Add TravisCI configuration

## 1.0.1
* Fix dependency to pheanstalk-5.3. #4

## 1.0.0
* Update README to be composer compliant.
* First stable tag version.
