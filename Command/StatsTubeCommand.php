<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Gives statistical information about the specified tube if it exists.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $stats = $pheanstalk->statsTube($tube);
        
        if (count($stats) === 0 ) {
            $output->writeln('<info>no stats.</info>');
        }
        
        foreach ($stats as $key => $information) {
            $output->writeln('<info>' . $key . '</info> : ' . $information);
        }
    }
}
