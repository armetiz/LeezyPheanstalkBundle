<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:list-tube')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('The names of all tubes on the server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $tubes = $pheanstalk->listTubes();

        if (count($tubes) === 0 ) {
            $output->writeln('Pheanstalk : <error>' . $pheanstalkName . '</error>');
            $output->writeln('<error>0</error> tube defined.');

            return;
        }

        $output->writeln('Pheanstalk : <info>' . $pheanstalkName . '</info>');

        foreach ($tubes as $tube) {
            $output->writeln('- ' . $tube);
        }
    }
}
