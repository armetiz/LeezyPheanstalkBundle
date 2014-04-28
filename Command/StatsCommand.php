<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->addOption('tubes', 't', InputOption::VALUE_NONE, 'Show detailed stats for all tubes.')
            ->setDescription('Gives statistical information about the beanstalkd system as a whole.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
            $stats = $pheanstalk->stats();

            if (count($stats) === 0 ) {
                $output->writeln('Pheanstalk : <error>' . $pheanstalkName . '</error>');
                $output->writeln('<info>0 stats.</info>');

                return;
            }

            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');

            foreach ($stats as $key => $information) {
                $output->writeln('- <info>' . $key . '</info> : ' . $information);
            }

            if ($input->getOption('split')) {
                $tubes = $pheanstalk->listTubes();

                // Fetch stats for each tube
                foreach ($tubes as $tube) {
                    $stats = (array) $pheanstalk->statsTube($tube);

                    if (count($stats) === 0 ) {
                        $output->writeln('Tube : <error>' . $tube . '</error>');
                        $output->writeln('<info>0 stats.</info>');
                    } else {
                        $output->writeln('');
                        $output->writeln('Tube:');
                        foreach ($stats as $key => $information) {
                            $output->writeln('- <info>' . $key . '</info> : ' . $information);
                        }
                    }
                }
            }
        } catch (\Pheanstalk_Exception $e) {
            $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
