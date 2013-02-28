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
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.', null)
            ->setDescription('Delete all job in a specific tube.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $connectionName = $input->getArgument('connection');
        
        $connectionLocator = $this->getContainer()->get('leezy.pheanstalk.connection_locator');
        $pheanstalk = $connectionLocator->getConnection($connectionName);
        
        if (null == $pheanstalk) {
            $output->writeln('Connection not found : <error>' . $connectionName . '</error>');
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
        catch (Pheanstalk_Exception_ConnectionException $ex) {
            
        }
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekBuried();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (Pheanstalk_Exception_ConnectionException $ex) {
            
        }
        
        try {
            while (true) {
                $job = $pheanstalk->useTube($tube)->peekReady();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        }
        catch (Pheanstalk_Exception_ConnectionException $ex) {
            
        }

        $output->writeln('Job deleted : <info>' . $numJobDelete . '</info>.');
    }
}
