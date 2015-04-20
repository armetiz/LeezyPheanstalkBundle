<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\PauseTubeCommand;
use Symfony\Component\Console\Tester\CommandTester;

class PauseTubeCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args  = $this->getCommandArgs();
        $tube  = $args['tube'];
        $delay = $args['delay'];

        $this->pheanstalk->expects($this->once())->method('pauseTube')->with($tube, $delay);

        $command = $this->application->find('leezy:pheanstalk:pause-tube');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        $this->assertContains(sprintf('Tube %s has been paused for %d seconds', $tube, $delay), $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new PauseTubeCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['tube' => 'default', 'delay' => 10];
    }
}
