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
            ->addArgument('tube', InputArgument::OPTIONAL, 'Tube to get stats.', null)
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.', 'default')
            ->setDescription('Gives statistical information about the specified tube if it exists.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalkName = $input->getArgument('pheanstalk');

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);

        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');

            return;
        }

        if (!$pheanstalk->getPheanstalk()->getConnection()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');

            return;
        }

        $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');

        try {
            $tubes = array();

            // if a tube argument is given, only consider that tube. If not, get stats for all tubes
            if ($tube = $input->getArgument('tube')) {
                $tubes[] = $tube;
            } else {
                $tubes = $pheanstalk->listTubes();
            };

            $whiteline = false;
            foreach ($tubes as $tube) {
                // fetch stats for each tube
                $stats = (array) $pheanstalk->statsTube($tube);

                if (count($stats) === 0 ) {
                    $output->writeln('Tube : <error>' . $tube . '</error>');
                    $output->writeln('<info>0 stats.</info>');
                } else {
                    // only add a whiteline if we are past the first tube (for BC)
                    if ($whiteline) {
                        $output->writeln('');
                    }

                    foreach ($stats as $key => $information) {
                        $output->writeln('- <info>' . $key . '</info> : ' . $information);
                    }
                }

                $whiteline = true;
            }
        } catch (Pheanstalk_Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
