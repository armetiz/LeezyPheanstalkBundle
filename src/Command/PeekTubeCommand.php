<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PeekTubeCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:peek-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'The tube to peek.')
            ->addOption('buried', 'b', InputOption::VALUE_NONE, 'Peek in buried instead of ready')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Take a peek at the first job in a tube, ready or burried.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube   = $input->getArgument('tube');
        $buried = $input->getOption('buried');
        $name   = $input->getArgument('pheanstalk');

        $pheanstalk = $this->getPheanstalk($name);

        try {
            if ($buried) {
                $job = $pheanstalk->peekBuried($tube);
            } else {
                $job = $pheanstalk->peekReady($tube);
            }

            if ($job) {
                $output->writeln(sprintf('Pheanstalk: <info>%s</info>', $name));
                $output->writeln(sprintf('Tube: <info>%s</info>', $tube));
                $output->writeln(sprintf('Job id: <info>%s</info>', $job->getId()));
                $output->writeln(sprintf('Data: <info>%s</info>', $job->getData()));
            }

            return 0;
        } catch (Exception $e) {
            $output->writeln(sprintf('Pheanstalk: <info>%s</info>', $name));
            $output->writeln(sprintf('Tube: <info>%s</info>', $tube));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
