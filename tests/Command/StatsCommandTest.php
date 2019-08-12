<?php

namespace Leezy\PheanstalkBundle\Tests\Command;

use Leezy\PheanstalkBundle\Command\StatsCommand;
use Pheanstalk\Response\ArrayResponse;
use Symfony\Component\Console\Tester\CommandTester;

class StatsCommandTest extends AbstractPheanstalkCommandTest
{
    public function testExecute()
    {
        $args  = $this->getCommandArgs();
        $stats = new ArrayResponse('STATS', [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'qux',
        ]);

        $this->pheanstalk->expects($this->once())->method('stats')->will($this->returnValue($stats));

        $command       = $this->application->find('leezy:pheanstalk:stats');
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
        return new StatsCommand($this->locator);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandArgs()
    {
        return [];
    }
}
