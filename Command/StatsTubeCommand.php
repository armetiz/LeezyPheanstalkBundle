<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube to get stats.')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.')
            ->setDescription('Gives statistical information about the specified tube if it exists.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $connectionName = $input->getArgument('connection');
        
        $connectionLocator = $this->getContainer()->get('leezy.pheanstalk.connection_locator');
        $pheanstalk = $connectionLocator->getConnection($connectionName);
        
        $stats = $pheanstalk->statsTube($tube);
        
        if (count($stats) === 0 ) {
            $output->writeln('<info>0 stats.</info>');
        }
        
        foreach ($stats as $key => $information) {
            $output->writeln('- <info>' . $key . '</info> : ' . $information);
        }
    }
}
