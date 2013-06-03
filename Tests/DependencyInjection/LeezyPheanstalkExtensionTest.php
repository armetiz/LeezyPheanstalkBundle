<?php

namespace Leezy\PheanstalkBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use Leezy\PheanstalkBundle\DependencyInjection\LeezyPheanstalkExtension;
use Leezy\PheanstalkBundle\LeezyPheanstalkBundle;

class LeezyPheanstalkExtensionTest extends \PHPUnit_Framework_TestCase
{
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

    public function testInitConfiguration()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
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

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.pheanstalk_locator'));
        $this->assertFalse($this->container->hasParameter('leezy.pheanstalk.pheanstalks'));
    }

    public function testDefaultPheanstalk()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
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

    public function testNoDefaultPheanstalk()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
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
    public function testTwoDefaultPheanstalks()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
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

    public function testMultiplePheanstalks()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
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

    public function testPheanstalkLocator()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
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

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.pheanstalk_locator'));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testPheanstalkProxyCustomTypeNotDefined()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
                    "primary" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60,
                        "proxy" => "acme.pheanstalk.pheanstalk_proxy"
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testPheanstalkReservedName()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
                    "proxy" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60,
                        "proxy" => "acme.pheanstalk.pheanstalk_proxy"
                    )
                )
            )
        );
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    public function testPheanstalkProxyCustomType()
    {
        $config = array(
            "leezy_pheanstalk" => array (
                "enabled" => true,
                "pheanstalks" => array (
                    "primary" => array (
                        "server" => "beanstalkd.domain.tld",
                        "port" => 11300,
                        "timeout" => 60,
                        "proxy" => "acme.pheanstalk.pheanstalk_proxy"
                    )
                )
            )
        );

        $this->container->setDefinition('acme.pheanstalk.pheanstalk_proxy', new Definition('Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface'));

        $this->extension->load($config, $this->container);
        $this->container->compile();
    }
}
