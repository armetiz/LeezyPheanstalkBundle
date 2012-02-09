<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk\Exception;

class DeleteJobCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:delete-job')
            ->addArgument('job', InputArgument::REQUIRED, 'Jod id to delete.')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.', "default")
            ->setDescription('Delete the specified job if it exists.')
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
        
        try {
            $job = $pheanstalk->peek($jobId);
            $pheanstalk->delete($job);
            
            $output->writeln('Job <info>' . $jobId . '</info> deleted.');
        }
        catch (Exception $ex) {
            $output->writeln('Job not found');
        }
        
    }
}
