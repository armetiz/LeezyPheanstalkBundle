<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\KickCommand;
use Symfony\Component\Console\Tester\CommandTester;

class KickCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args = $this->getCommandArgs();

        $this->pheanstalk->expects($this->once())->method('useTube')->with($args['tube']);
        $this->pheanstalk->expects($this->once())->method('kick')->with($args['max'])->will($this->returnValue(3));

        $command = $this->application->find('leezy:pheanstalk:kick');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        $this->assertContains('3 Job(s) have been kicked from test', $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new KickCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['tube' => 'test', 'max' => 10];
    }
}
