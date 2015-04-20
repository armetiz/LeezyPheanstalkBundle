<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTubeCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:list-tube')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('The names of all tubes on the server.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name       = $input->getArgument('pheanstalk');
        $pheanstalk = $this->getPheanstalk($name);

        $tubes = $pheanstalk->listTubes();

        if (count($tubes) === 0) {
            $output->writeln('Pheanstalk: <error>'.$name.'</error>');
            $output->writeln('<error>0</error> tube defined.');

            return 0;
        }

        $output->writeln('Pheanstalk: <info>'.$name.'</info>');

        foreach ($tubes as $tube) {
            $output->writeln('- '.$tube);
        }

        return 0;
    }
}
