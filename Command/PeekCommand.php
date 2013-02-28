<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PeekCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:peek')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to peek.')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.')
            ->setDescription('Inspect a job in the system, regardless of what tube it is in.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job');
        $connectionName = $input->getArgument('connection');
        
        $connectionLocator = $this->getContainer()->get('leezy.pheanstalk.connection_locator');
        $pheanstalk = $connectionLocator->getConnection($connectionName);
        
        if (null == $pheanstalk) {
            $output->writeln('Connection not found : <error>' . $connectionName . '</error>');
            return;
        }
        
        $job = $pheanstalk->peek ($jobId);
        
        if ($job) {
            $output->writeln('Job id : <info>' . $job->getId() . '</info>');
            $output->writeln('Data : <info>' . $job->getData() . '</info>');
        }
        else {
            $output->writeln('No valid job found');
        }
    }
}
