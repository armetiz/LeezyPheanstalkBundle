<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_Exception_CommandException;

class PeekCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:peek')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to peek.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Inspect a job in the system, regardless of what tube it is in.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job');
        $pheanstalkName = $input->getArgument('pheanstalk');

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);

        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');

            return;
        }

        if (null === $pheanstalkName) {
            $pheanstalkName = 'default';
        }

        if (!$pheanstalk->getPheanstalk()->getConnection()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');

            return;
        }

        try {
            $job = $pheanstalk->peek ($jobId);

            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');
            $output->writeln('Job id : <info>' . $job->getId() . '</info>');
            $output->writeln('Data : <info>' . $job->getData() . '</info>');
        }
        catch (Pheanstalk_Exception_CommandException $ex) {
            $output->writeln('Pheanstalk : <error>' . $pheanstalkName . '</error>');
            $output->writeln('No valid job found');
        }
    }
}
