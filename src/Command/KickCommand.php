<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KickCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:kick')
            ->addArgument('tube', InputArgument::REQUIRED, 'The tube to kick the jobs from.')
            ->addArgument('max', InputArgument::OPTIONAL, 'The maximum job to kick from this tube.', 1)
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Kick buried jobs from a specific tube.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube       = $input->getArgument('tube');
        $max        = $input->getArgument('max');
        $name       = $input->getArgument('pheanstalk');
        $pheanstalk = $this->getPheanstalk($name);

        try {
            $pheanstalk->useTube($tube);
            $numJobKicked = $pheanstalk->kick($max);

            $output->writeln('Pheanstalk: <info>'.$name.'</info>');

            if ($numJobKicked > 0) {
                $output->writeln(sprintf('%d Job(s) have been kicked from %s', $numJobKicked, $tube));
            } else {
                $output->writeln('No jobs to kick were found');
            }

            return 0;
        } catch (Exception $e) {
            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
