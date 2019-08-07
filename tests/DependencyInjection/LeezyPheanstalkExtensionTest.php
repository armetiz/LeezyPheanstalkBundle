<?php

namespace Leezy\PheanstalkBundle\Tests\DependencyInjection;

use Leezy\PheanstalkBundle\DependencyInjection\LeezyPheanstalkExtension;
use Leezy\PheanstalkBundle\LeezyPheanstalkBundle;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LeezyPheanstalkExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var LeezyPheanstalkExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new LeezyPheanstalkExtension();

        $bundle = new LeezyPheanstalkBundle();
        $bundle->build($this->container); // Attach all default factories
    }

    protected function tearDown(): void
    {
        unset($this->container, $this->extension);
    }

    public function testInitConfiguration()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'default' => true,
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.pheanstalk_locator'));
        $this->assertTrue($this->container->hasParameter('leezy.pheanstalk.pheanstalks'));  // Needed by ProxyCompilerPass
    }

    public function testDefaultPheanstalk()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'default' => true,
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.primary'));
        $this->assertTrue($this->container->hasAlias('leezy.pheanstalk'));
    }

    public function testNoDefaultPheanstalk()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.primary'));
        $this->assertFalse($this->container->hasAlias('leezy.pheanstalk'));
    }

    /**
     * @expectedException \Leezy\PheanstalkBundle\Exceptions\PheanstalkException
     */
    public function testTwoDefaultPheanstalks()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'one' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'default' => true,
                    ],
                    'two' => [
                        'server'  => 'beanstalkd-2.domain.tld',
                        'default' => true,
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    public function testMultiplePheanstalks()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'one'   => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                    ],
                    'two'   => [
                        'server' => 'beanstalkd-2.domain.tld',
                    ],
                    'three' => [
                        'server' => 'beanstalkd-3.domain.tld',
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.one'));
        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.two'));
        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.three'));

        # @see https://github.com/armetiz/LeezyPheanstalkBundle/issues/61
        $this->assertNotSame($this->container->getDefinition('leezy.pheanstalk.one'), $this->container->getDefinition('leezy.pheanstalk.two'));
    }

    public function testPheanstalkLocator()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'default' => true,
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.pheanstalk_locator'));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testPheanstalkProxyCustomTypeNotDefined()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'proxy'   => 'acme.pheanstalk.pheanstalk_proxy',
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testPheanstalkReservedName()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'proxy' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'proxy'   => 'acme.pheanstalk.pheanstalk_proxy',
                    ],
                ],
            ],
        ];
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    public function testPheanstalkProxyCustomType()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'proxy'   => 'acme.pheanstalk.pheanstalk_proxy',
                    ],
                ],
            ],
        ];

        $this->container->setDefinition('acme.pheanstalk.pheanstalk_proxy', new Definition(PheanstalkProxy::class));

        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertNotNull($this->container->get('leezy.pheanstalk.primary'));
    }

    public function testLoggerConfiguration()
    {
        $config = [
            'leezy_pheanstalk' => [
                'pheanstalks' => [
                    'primary' => [
                        'server'  => 'beanstalkd.domain.tld',
                        'port'    => 11300,
                        'timeout' => 60,
                        'default' => true,
                    ],
                ],
            ],
        ];

        $this->container->setDefinition('logger', new Definition(NullLogger::class));

        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('leezy.pheanstalk.listener.log'));
        $listener = $this->container->getDefinition('leezy.pheanstalk.listener.log');

        $this->assertTrue($listener->hasMethodCall('setLogger'));
        $this->assertTrue($listener->hasTag('monolog.logger'));

        $tag = $listener->getTag('monolog.logger');
        $this->assertEquals('pheanstalk', $tag[0]['channel']);
    }
}
