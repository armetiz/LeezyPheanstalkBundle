<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsTubeCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:stats-tube')
            ->addArgument('tube', InputArgument::OPTIONAL, 'Tube to get stats for.', null)
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.', null)
            ->setDescription('Gives statistical information about a specified tube, or about all tubes.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name       = $input->getArgument('pheanstalk');
        $pheanstalk = $this->getPheanstalk($name);

        $output->writeln('Pheanstalk: <info>'.$name.'</info>');

        try {
            $tubes = [];

            // if a tube argument is given, only consider that tube. If not, get stats for all tubes
            if ($tube = $input->getArgument('tube')) {
                $tubes[] = $tube;
            } else {
                $tubes = $pheanstalk->listTubes();
            };

            $whiteline = false;
            foreach ($tubes as $tube) {
                // fetch stats for each tube
                $stats = $pheanstalk->statsTube($tube);

                if (count($stats) === 0) {
                    $output->writeln('Tube: <error>'.$tube.'</error>');
                    $output->writeln('<info>0 stats.</info>');
                } else {
                    // only add a whiteline if we are past the first tube (for BC)
                    if ($whiteline) {
                        $output->writeln('Tube:');
                    }

                    foreach ($stats as $key => $information) {
                        $output->writeln('- <info>'.$key.'</info>: '.$information);
                    }
                }

                $whiteline = true;
            }

            return 0;
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }
}
