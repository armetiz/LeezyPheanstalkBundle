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
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Kick buried jobs from a specific tube.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $max = $input->getArgument('max');
        $pheanstalkName = $input->getArgument('pheanstalk');

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        $pheanstalk = $pheanstalkLocator->getConnection($pheanstalkName);

        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');
            return;
        }

        if (!$pheanstalk->getPheanstalk()->getConnection()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');
            return;
        }

        try
        {
            $numJobKicked = 0;
            $numJobKicked = $pheanstalk->useTube($tube)->kick($max);

            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');

            if ($numJobKicked > 0) {
                $output->writeln(sprintf('%d Job(s) have been kicked from %s', $numJobKicked, $tube));
            }
            else {
                $output->writeln('No job to kicked were found');
            }
        }
        catch(Pheanstalk_Exception_PheanstalkException $e)
        {
            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');
            $output->writeln(sprintf('%d Job(s) have been kicked from %s', $numJobKicked, $tube));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
