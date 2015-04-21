<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\NextReadyCommand;
use Pheanstalk\Job;
use Symfony\Component\Console\Tester\CommandTester;

class NextReadyCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args = $this->getCommandArgs();
        $data = 'foobar';
        $job  = new Job(1234, $data);

        $this->pheanstalk->expects($this->once())->method('peekReady')->will($this->returnValue($job));

        $command = $this->application->find('leezy:pheanstalk:next-ready');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        $this->assertContains(sprintf('Next ready job in tube default is %d', $job->getId()), $commandTester->getDisplay());
        $this->assertContains($data, $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new NextReadyCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['tube' => 'default', '--details' => true];
    }
}
