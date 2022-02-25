<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\PeekTubeCommand;
use Pheanstalk\Job;
use Symfony\Component\Console\Tester\CommandTester;

class PeekTubeCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args = $this->getCommandArgs();
        $tube = $args['tube'];
        $job  = new Job(1234, 'data');

        $this->pheanstalk->expects($this->once())->method('useTube')->with($tube)->will($this->returnValue($this->pheanstalk));
        $this->pheanstalk->expects($this->once())->method('peekBuried')->will($this->returnValue($job));

        $command = $this->application->find('leezy:pheanstalk:peek-tube');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        $this->assertStringContainsString(sprintf('Tube: %s', $tube), $commandTester->getDisplay());
        $this->assertStringContainsString(sprintf('Job id: %s', $job->getId()), $commandTester->getDisplay());
        $this->assertStringContainsString(sprintf('Data: %s', $job->getData()), $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new PeekTubeCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['tube' => 'default', '--buried' => true];
    }
}
