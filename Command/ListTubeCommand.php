<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('The names of all tubes on the server.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $tubes = $pheanstalk->listTubes();
        
        if (count($tubes) === 0 ) {
            $output->writeln('<info>no tube defined.</info>');
        }
        
        foreach ($tubes as $tube) {
            $output->writeln('<info>' . $tube . '</info>');
        }
    }
}
