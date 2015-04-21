<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception\ServerException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlushTubeCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:flush-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Delete all job in a specific tube.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube       = $input->getArgument('tube');
        $name       = $input->getArgument('pheanstalk');
        $pheanstalk = $this->getPheanstalk($name);

        $numJobDelete = 0;

        try {
            $pheanstalk->useTube($tube);
            while (true) {
                $job = $pheanstalk->peekDelayed();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        } catch (ServerException $ex) {
        }

        try {
            $pheanstalk->useTube($tube);
            while (true) {
                $job = $pheanstalk->peekBuried();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        } catch (ServerException $ex) {
        }

        try {
            $pheanstalk->useTube($tube);
            while (true) {
                $job = $pheanstalk->peekReady();
                $pheanstalk->delete($job);
                $numJobDelete++;
            }
        } catch (ServerException $ex) {
        }

        $output->writeln('Pheanstalk: <info>'.$name.'</info>');
        $output->writeln('Jobs deleted: <info>'.$numJobDelete.'</info>.');

        return 0;
    }
}
