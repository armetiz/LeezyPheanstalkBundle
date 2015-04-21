<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteJobCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:delete-job')
            ->addArgument('job', InputArgument::REQUIRED, 'Jod id to delete.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Delete the specified job if it exists.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId      = $input->getArgument('job');
        $name       = $input->getArgument('pheanstalk');
        $pheanstalk = $this->getPheanstalk($name);

        try {
            $job = $pheanstalk->peek($jobId);
            $pheanstalk->delete($job);

            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln('Job <info>'.$jobId.'</info> deleted.');

            return 0;
        } catch (Exception $e) {
            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
