<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PauseTubeCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:pause-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'The tube to pause')
            ->addArgument('delay', InputArgument::REQUIRED, 'Seconds before jobs may be reserved from this queue.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Temporarily prevent jobs being reserved from the given tube.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube  = $input->getArgument('tube');
        $delay = $input->getArgument('delay');
        $name  = $input->getArgument('pheanstalk');

        $pheanstalk = $this->getPheanstalk($name);

        try {
            $pheanstalk->pauseTube($tube, $delay);

            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln('Tube <info>'.$tube.'</info> has been paused for <info>'.$delay.'</info> seconds.');

            return 0;
        } catch (Exception $e) {
            $output->writeln('Pheanstalk: <info>'.$name.'</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
