<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\FlushTubeCommand;
use Pheanstalk\Exception\ServerException;
use Pheanstalk\Job;
use Pheanstalk\JobId;
use Symfony\Component\Console\Tester\CommandTester;

class FlushTubeCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $job = new Job(1234, 'test');

        $this->pheanstalk->expects($this->atLeast(3))->method('useTube');
        $this->pheanstalk->expects($this->atLeast(3))->method('delete')->with(new JobId($job->getId()));

        $jobs = [];
        foreach (['peekDelayed', 'peekBuried', 'peekReady'] as $method) {
            $jobs[$method] = [$job];
            $this->pheanstalk->expects($this->any())->method($method)->willReturnCallback(function () use (&$jobs, $method) {
                if (!empty($jobs[$method])) {
                    return array_shift($jobs[$method]);
                }

                throw new ServerException('Server reported NOT_FOUND');
            });
        }

        $command = $this->application->find('leezy:pheanstalk:flush-tube');
        $commandTester = new CommandTester($command);
        $commandTester->execute($this->getCommandArgs());

        $this->assertContains('Jobs deleted: 3', $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new FlushTubeCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['tube' => 'default'];
    }
}
