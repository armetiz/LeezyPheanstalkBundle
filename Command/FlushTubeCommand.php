<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use \Pheanstalk_Exception_ConnectionException;

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
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Delete all job in a specific tube.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
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
        
        $numJobDelete = 0;
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekDelayed();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (Pheanstalk_Exception_PheanstalkException $ex) {
            
        }
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekBuried();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (Pheanstalk_Exception_PheanstalkException $ex) {
            
        }
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekReady();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (Pheanstalk_Exception_PheanstalkException $ex) {
            
        }

        $output->writeln('Job deleted : <info>' . $numJobDelete . '</info>.');
    }
}
