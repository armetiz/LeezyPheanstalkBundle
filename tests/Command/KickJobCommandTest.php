<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\KickJobCommand;
use Pheanstalk\Job;
use Pheanstalk\JobId;
use Symfony\Component\Console\Tester\CommandTester;

class KickJobCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args = $this->getCommandArgs();
        $jobId  = new JobId($args['job']);

        $this->pheanstalk->expects($this->once())->method('kickJob')->with($jobId);

        $command = $this->application->find('leezy:pheanstalk:kick-job');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        $this->assertContains(sprintf('Job #%d has been kicked', $jobId->getId()), $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new KickJobCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['job' => 1234];
    }
}
