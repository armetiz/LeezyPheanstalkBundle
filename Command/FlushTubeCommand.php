<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FlushTubeCommand extends ContainerAwareCommand
{
    private $states = array(
        \Pheanstalk_Command_PeekCommand::TYPE_READY,
        \Pheanstalk_Command_PeekCommand::TYPE_BURIED,
        \Pheanstalk_Command_PeekCommand::TYPE_DELAYED
    );

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:flush-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->addOption('state', null, InputOption::VALUE_OPTIONAL, sprintf('Job State name (%s).', implode(', ', $this->states)))
            ->setDescription(sprintf('Delete all jobs in a specific tube, with an optional job state (%s).', implode(', ', $this->states)));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $pheanstalkName = $input->getArgument('pheanstalk');
        $stateName = $input->getOption('state');

        if ($stateName && !in_array($stateName, $this->states)) {
            $output->writeln('Job State name: <error>' . $stateName . '</error>. Should be ' . implode(' || ', $this->states));

            return;
        }

        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);

        if (null === $pheanstalkName) {
            $pheanstalkName = 'default';
        }

        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found: <error>' . $pheanstalkName . '</error>');

            return;
        }

        if (!$pheanstalk->getPheanstalk()->getConnection()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected: <error>' . $pheanstalkName . '</error>');

            return;
        }

        $numJobDelete = 0;

        foreach ($this->states as $state) {
            if ($state === $stateName || !$stateName) {
                $method = 'peek' . ucfirst($state);
                try {
                    while (true) {
                        $job = $pheanstalk->useTube($tube)->$method();
                        $pheanstalk->delete($job);
                        $numJobDelete++;
                    }
                } catch (\Pheanstalk_Exception_ServerException $ex) {

                }
            }
        }

        $output->writeln('Pheanstalk: <info>' . $pheanstalkName . '</info>');
        $output->writeln('Tube: <info>' . $tube . '</info>');
        $output->writeln(sprintf('Job deleted %s', !$stateName ? 'from all job states' : sprintf('from state %s', ucfirst($stateName))) . ': <info>' . $numJobDelete . '</info>.');
    }
}
