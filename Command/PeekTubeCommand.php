<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_Exception;

class PeekTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $buried = $input->getOption('buried');

        $pheanstalkName = $input->getArgument('pheanstalk');

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        /** @var \Pheanstalk_Pheanstalk $pheanstalk */
        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);

        if (null === $pheanstalkName) {
            $pheanstalkName = 'default';
        }

        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');

            return;
        }

        if (!$pheanstalk->getPheanstalk()->getConnection()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');

            return;
        }

        try {
            if ($buried) {
                $job = $pheanstalk->peekBuried($tube);
            } else {
                $job = $pheanstalk->peekReady($tube);
            }

            if ($job) {
                $output->writeln(sprintf('Pheanstalk : <info>%s</info>', $pheanstalkName));
                $output->writeln(sprintf('Tube : <info>%s</info>', $tube));
                $output->writeln(sprintf('Job id : <info>%s</info>', $job->getId()));
                $output->writeln(sprintf('Data : <info>%s</info>', $job->getData()));
            }
        } catch (Pheanstalk_Exception $e) {
            $output->writeln(sprintf('Pheanstalk : <info>%s</info>', $pheanstalkName));
            $output->writeln(sprintf('Tube : <info>%s</info>', $tube));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
