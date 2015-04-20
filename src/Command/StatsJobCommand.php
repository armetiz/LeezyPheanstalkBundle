<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsJobCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats-job')
            ->addArgument('job', InputArgument::REQUIRED, 'Jod id to get stats.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Gives statistical information about the specified job if it exists.');
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
            $job   = $pheanstalk->peek($jobId);
            $stats = $pheanstalk->statsJob($job);

            if (count($stats) === 0) {
                $output->writeln('Pheanstalk: <error>'.$name.'</error>');
                $output->writeln('<info>0 stats.</info>');

                return 0;
            }

            $output->writeln('Pheanstalk: <info>'.$name.'</info>');

            foreach ($stats as $key => $information) {
                $output->writeln('- <info>'.$key.'</info>: '.$information);
            }

            return 0;
        } catch (Exception $e) {
            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
