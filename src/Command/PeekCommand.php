<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PeekCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:peek')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to peek.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Inspect a job in the system, regardless of what tube it is in.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job');
        $name  = $input->getArgument('pheanstalk');

        $pheanstalk = $this->getPheanstalk($name);

        try {
            $job = $pheanstalk->peek($jobId);

            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln('Job id: <info>'.$job->getId().'</info>');
            $output->writeln('Data: <info>'.$job->getData().'</info>');

            return 0;
        } catch (Exception $e) {
            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
