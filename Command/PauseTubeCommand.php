<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PauseTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:pause-tube')
            ->setDescription('Temporarily prevent jobs being reserved from the given tube.')
            ->addArgument('tube', InputArgument::REQUIRED, 'The tube to pause')
            ->addArgument('delay', InputArgument::REQUIRED, 'Seconds before jobs may be reserved from this queue.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $delay = $input->getArgument('delay');
        
        $pheanstalk = $this->getContainer()->get("leezy.pheanstalk");
        $pheanstalk->pauseTube ($tube, $delay);
        
        $output->writeln('Tube <info>' . $tube . '</info> have been paused for <info>' . $delay . '</info> seconds.');
    }
}
