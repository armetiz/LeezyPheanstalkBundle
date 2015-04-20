<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\PutCommand;
use Symfony\Component\Console\Tester\CommandTester;

class PutCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args     = $this->getCommandArgs();
        $tube     = $args['tube'];
        $data     = $args['data'];
        $priority = $args['priority'];
        $delay    = $args['delay'];
        $ttr      = $args['ttr'];
        $jobId    = 1234;

        $this->pheanstalk->expects($this->once())->method('useTube')->with($tube);
        $this->pheanstalk->expects($this->once())->method('put')->with($data, $priority, $delay, $ttr)->will($this->returnValue($jobId));

        $command       = $this->application->find('leezy:pheanstalk:put');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        $this->assertContains(sprintf('New job on tube %s with id %d', $tube, $jobId), $commandTester->getDisplay());
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new PutCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return ['tube' => 'default', 'data' => 'foobar', 'priority' => 512, 'delay' => 10, 'ttr' => 120];
    }
}
