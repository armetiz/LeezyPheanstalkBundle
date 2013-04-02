<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KickCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:kick')
            ->addArgument('tube', InputArgument::REQUIRED, 'The tube to kick the jobs from.')
            ->addArgument('max', InputArgument::OPTIONAL, 'The maximum job to kick from this tube.', 1)
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection name.', "default")
            ->setDescription('Kick buried jobs from a specific tube.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $max = $input->getArgument('max');
        $connectionName = $input->getArgument('connection');
        
        $connectionLocator = $this->getContainer()->get('leezy.pheanstalk.connection_locator');
        $pheanstalk = $connectionLocator->getConnection($connectionName);
        
        if (null == $pheanstalk) {
            $output->writeln('Connection not found : <error>' . $connectionName . '</error>');
            return;
        }

        /** @var \Pheanstalk_Pheanstalk $pheanstalk */
        $kicked = $pheanstalk->useTube($tube)->kick($max);

        if ($kicked > 0) {
            $output->writeln(sprintf('%d Job(s) have been kicked from %s', $kicked, $tube));
        }
        else {
            $output->writeln('No job to kicked were found');
        }
    }
}
