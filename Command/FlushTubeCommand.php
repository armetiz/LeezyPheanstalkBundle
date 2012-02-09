<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FlushTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:flush-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube.')
            ->setDescription('Delete all job in a specific tube.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $numJobDelete = 0;
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekDelayed();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (\Pheanstalk_Exception $ex) {
            
        }
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekBuried();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (\Pheanstalk_Exception $ex) {
            
        }
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekReady();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (\Pheanstalk_Exception $ex) {
            
        }

        $output->writeln('Job deleted : <info>' . $numJobDelete . '</info>.');
    }
}
