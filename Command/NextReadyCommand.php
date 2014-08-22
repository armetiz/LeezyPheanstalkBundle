<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NextReadyCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:next-ready')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube to get next ready.', null)
            ->addOption(
                'details',
                null,
                InputOption::VALUE_OPTIONAL,
                'Display details',
                false
            )
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Gives the next ready job from a specified tube.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalkName = $input->getArgument('pheanstalk');
        $tubeName = $input->getArgument('tube');

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

        $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');

        try {
            $nextJobReady       = $pheanstalk->peekReady($tubeName);
            $nextJobReadyId     = $nextJobReady->getId();
            $nextJobReadyData   = $nextJobReady->getData();

            $output->writeln(
                sprintf('Next ready job in tube <info>%s</info> is <info>%s</info>', $tubeName, $nextJobReadyId)
            );

            if((bool)$input->getOption('details')) {
                $output->writeln('Details :');
                $output->writeln($nextJobReadyData);
            }
        }
        catch (\Pheanstalk_Exception_ServerException $e) {
            $output->writeln('There is no next ready job in this tube : <info>' . $tubeName . '</info>');

            return;
        }
    }
}
