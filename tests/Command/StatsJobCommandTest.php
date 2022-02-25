<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\StatsJobCommand;
use Pheanstalk\JobId;
use Pheanstalk\Response\ArrayResponse;
use Symfony\Component\Console\Tester\CommandTester;

class StatsJobCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args  = $this->getCommandArgs();
        $jobId = new JobId($args['job']);
        $stats = new ArrayResponse('STATS', [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'qux',
        ]);

        $this->pheanstalk->expects($this->once())->method('statsJob')->with($jobId)->will($this->returnValue($stats));

        $command       = $this->application->find('leezy:pheanstalk:stats-job');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        foreach ($stats as $key => $value) {
            $this->assertStringContainsString(sprintf('- %s: %s', $key, $value), $commandTester->getDisplay());
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
