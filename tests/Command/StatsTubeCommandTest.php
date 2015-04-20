<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\StatsTubeCommand;
use Symfony\Component\Console\Tester\CommandTester;

class StatsTubeCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args  = $this->getCommandArgs();
        $tube  = 'default';
        $stats = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'qux',
        ];

        $this->pheanstalk->expects($this->once())->method('listTubes')->will($this->returnValue([$tube]));
        $this->pheanstalk->expects($this->once())->method('statsTube')->with($tube)->will($this->returnValue($stats));

        $command       = $this->application->find('leezy:pheanstalk:stats-tube');
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
        return new StatsTubeCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return [];
    }
}
