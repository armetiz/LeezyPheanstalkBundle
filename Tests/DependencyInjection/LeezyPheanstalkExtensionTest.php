<?php

namespace Leezy\PheanstalkBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Leezy\PheanstalkBundle\DependencyInjection\LeezyPheanstalkExtension;
use Leezy\PheanstalkBundle\LeezyPheanstalkBundle;

class LeezyPheanstalkExtensionTest extends \PHPUnit_Framework_TestCase {
    private $container;
    private $extension;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new LeezyPheanstalkExtension();
        
        $bundle = new LeezyPheanstalkBundle();
        $bundle->build($this->container); // Attach all default factories
    }

    public function tearDown()
    {
        unset($this->container, $this->extension);
    }
    
    public function testDefaultConnection()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "connection" => array (
                    "primary" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60,
                        "default" => true
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.primary'));
        $this->assertTrue($this->container->hasAlias('leezy.pheanstalk'));
    }
    
    public function testNoDefaultConnection()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "connection" => array (
                    "primary" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60,
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.primary'));
        $this->assertFalse($this->container->hasAlias('leezy.pheanstalk'));
    }
    
    /**
     * @expectedException Leezy\PheanstalkBundle\Exceptions\PheanstalkException
     */
    public function testTwoDefaultConnections()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "connection" => array (
                    "one" => array (
                        "server" => "beanstalkd.domain.tld",
                        "default" => true
                    ),
                    "two" => array (
                        "server" => "beanstalkd-2.domain.tld",
                        "default" => true
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }
    
    public function testMultipleConnections()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "connection" => array (
                    "one" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60
                    ),
                    "two" => array (
                        "server" => "beanstalkd-2.domain.tld",
                    ),
                    "three" => array (
                        "server" => "beanstalkd-3.domain.tld",
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.one'));
        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.two'));
        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.three'));
    }
    
    public function testConnectionLocator()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "connection" => array (
                    "primary" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60,
                        "default" => true
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.connection_locator'));
    }
}
