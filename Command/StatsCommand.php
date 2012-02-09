<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats')
            ->setDescription('Gives statistical information about the beanstalkd system as a whole.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $stats = $pheanstalk->stats();
        
        if (count($stats) === 0 ) {
            $output->writeln('<info>no stats.</info>');
        }
        
        foreach ($stats as $key => $information) {
            $output->writeln('<info>' . $key . '</info> : ' . $information);
        }
    }
}
