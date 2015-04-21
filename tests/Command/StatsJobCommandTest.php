<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\StatsJobCommand;
use Pheanstalk\Job;
use Symfony\Component\Console\Tester\CommandTester;

class StatsJobCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args  = $this->getCommandArgs();
        $jobId = $args['job'];
        $job   = new Job($jobId, 'data');
        $stats = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'qux',
        ];

        $this->pheanstalk->expects($this->once())->method('peek')->with($jobId)->will($this->returnValue($job));
        $this->pheanstalk->expects($this->once())->method('statsJob')->with($job)->will($this->returnValue($stats));

        $command       = $this->application->find('leezy:pheanstalk:stats-job');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        foreach ($stats as $key => $value) {
            $this->assertContains(sprintf('- %s: %s', $key, $value), $commandTester->getDisplay());
        }
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new StatsJobCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['job' => 1234];
    }
}
