<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\ListTubeCommand;
use Symfony\Component\Console\Tester\CommandTester;

class ListTubeCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args  = $this->getCommandArgs();
        $tubes = ['foo', 'bar'];

        $this->pheanstalk->expects($this->once())->method('listTubes')->will($this->returnValue($tubes));

        $command = $this->application->find('leezy:pheanstalk:list-tube');
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        foreach ($tubes as $tube) {
            $this->assertStringContainsString('- '.$tube, $commandTester->getDisplay());
        }
    }

    /**
     * @inheritdoc
     */
    protected function getCommand()
    {
        return new ListTubeCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return [];
    }
}
