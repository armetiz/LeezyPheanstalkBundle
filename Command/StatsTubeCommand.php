<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_Exception;

class StatsTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube to get stats.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Gives statistical information about the specified tube if it exists.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $pheanstalkName = $input->getArgument('pheanstalk');

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
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
            $stats = $pheanstalk->statsTube($tube);

            if (count($stats) === 0 ) {
                $output->writeln('Pheanstalk : <error>' . $pheanstalkName . '</error>');
                $output->writeln('<info>0 stats.</info>');
            } else {
                $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');

                foreach ($stats as $key => $information) {
                    $output->writeln('- <info>' . $key . '</info> : ' . $information);
                }
            }
        } catch (Pheanstalk_Exception $e) {
            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
