<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_Exception;

class KickJobCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:kick-job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job id to kick.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Kick the specified job if it has a valid buried status, regardless of what tube it is in.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job');
        $pheanstalkName = $input->getArgument('pheanstalk');

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');

        if (null === $pheanstalkName) {
            $pheanstalkName = 'default';
        }

        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);

        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');
            return;
        }

        if (!$pheanstalk->getPheanstalk()->getConnection()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');
            return;
        }

        try {
            $job = $pheanstalk->peek($jobId);
            $pheanstalk->kickJob($job);

            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');
            $output->writeln(sprintf('The job #%d has been kicked.', $jobId));

        } catch(Pheanstalk_Exception $e) {
            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
