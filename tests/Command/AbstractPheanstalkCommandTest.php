<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\AbstractPheanstalkCommand;
use Leezy\PheanstalkBundle\PheanstalkLocator;
use Pheanstalk\Connection;
use Pheanstalk\Contract\PheanstalkInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractPheanstalkCommandTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|KernelInterface
     */
    protected $kernel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PheanstalkLocator
     */
    protected $locator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PheanstalkInterface
     */
    protected $pheanstalk;

    /**
     * @var Application
     */
    protected $application;

    protected function setUp(): void
    {
        $this->kernel  = $this->getMockForAbstractClass(KernelInterface::class);

        $connection = $this->createConnectionMock();

        $this->pheanstalk = $this->createPheanstalkMock($connection);
        $this->locator    = $this->createLocatorMock($this->pheanstalk);

        $this->application = new Application();
        $this->application->add($this->getCommand());
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Pheanstalk not found: default
     */
    public function testPheanstalkNotFound()
    {
        $this->locator = $this->createLocatorMock();

        $command = $this->getCommand();

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute($this->getCommandArgs());
    }

    /**
     * @return AbstractPheanstalkCommand
     */
    abstract protected function getCommand();

    /**
     * @return array
     */
    abstract protected function getCommandArgs();

    /**
     * @param PheanstalkInterface $pheanstalk
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|PheanstalkLocator
     */
    private function createLocatorMock(PheanstalkInterface $pheanstalk = null)
    {
        $locator = $this
            ->getMockBuilder(PheanstalkLocator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPheanstalk'])
            ->getMock()
        ;

        $locator
            ->expects($this->any())
            ->method('getPheanstalk')
            ->will($this->returnValue($pheanstalk))
        ;

        return $locator;
    }

    /**
     * @param Connection $connection
     *
     * @return PheanstalkInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createPheanstalkMock(Connection $connection)
    {
        $pheanstalk = $this
            ->getMockBuilder(PheanstalkInterface::class)
            ->getMockForAbstractClass()
        ;

//        $pheanstalk->expects($this->any())->method('getConnection')->will($this->returnValue($connection));

        return $pheanstalk;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    protected function createConnectionMock()
    {
        return $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
    }
}
