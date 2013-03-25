<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use \Pheanstalk_Exception_CommandException;

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
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Delete the specified job if it exists.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job');
        $pheanstalkName = $input->getArgument('pheanstalk');
        
        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);
        
        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');
            return;
        }
        
        if (!$pheanstalk->getPheanstalk()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');
            return;
        }
        
        try {
            $job = $pheanstalk->peek($jobId);
            $pheanstalk->delete($job);
            
            $output->writeln('Job <info>' . $jobId . '</info> deleted.');
        }
        catch (Pheanstalk_Exception_CommandException $ex) {
            $output->writeln('Job not found');
        }
        
    }
}
