<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsJobCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats-job')
            ->addArgument('job', InputArgument::REQUIRED, 'Jod id to get stats.')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.', "default")
            ->setDescription('Gives statistical information about the specified job if it exists.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job');
        $connectionName = $input->getArgument('connection');
        
        $connectionFinder = new ConnectionFinder ($this->getContainer());
        $pheanstalk = $connectionFinder->getConnection($connectionName);
        
        if (null == $pheanstalk) {
            $output->writeln('Connection not found : <error>' . $connectionName . '</error>');
            return;
        }
        
        $job = $pheanstalk->peek($jobId);
        $stats = $pheanstalk->statsJob($job);
        
        if (count($stats) === 0 ) {
            $output->writeln('<info>0 stats.</info>');
        }
        
        foreach ($stats as $key => $information) {
            $output->writeln('- <info>' . $key . '</info> : ' . $information);
        }
    }
}
