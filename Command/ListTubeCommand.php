<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:list-tube')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.', "default")
            ->setDescription('The names of all tubes on the server.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connectionName = $input->getArgument('connection');
        
        $connectionLocator = $this->getContainer()->get('leezy.pheanstalk.connection_locator');
        $pheanstalk = $connectionLocator->getConnection($connectionName);
        
        if (null == $pheanstalk) {
            $output->writeln('Connection not found : <error>' . $connectionName . '</error>');
            return;
        }
        
        $tubes = $pheanstalk->listTubes();
        
        if (count($tubes) === 0 ) {
            $output->writeln('<info>0</info> tube defined.');
        }
        
        foreach ($tubes as $tube) {
            $output->writeln('- <info>' . $tube . '</info>');
        }
    }
}
