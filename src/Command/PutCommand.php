<?php

namespace Leezy\PheanstalkBundle\Command;

use Pheanstalk\PheanstalkInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PutCommand extends AbstractPheanstalkCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:put')
            ->addArgument('tube', InputArgument::REQUIRED, 'Tube to put job.')
            ->addArgument('data', InputArgument::REQUIRED, 'The job data.')
            ->addArgument('priority', InputArgument::OPTIONAL, 'From 0 (most urgent) to 0xFFFFFFFF (least urgent).', PheanstalkInterface::DEFAULT_PRIORITY)
            ->addArgument('delay', InputArgument::OPTIONAL, 'Seconds to wait before job becomes ready.', PheanstalkInterface::DEFAULT_DELAY)
            ->addArgument('ttr', InputArgument::OPTIONAL, 'Time To Run: seconds a job can be reserved for.', PheanstalkInterface::DEFAULT_TTR)
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Puts a job on the queue.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube     = $input->getArgument('tube');
        $data     = $input->getArgument('data');
        $priority = $input->getArgument('priority');
        $delay    = $input->getArgument('delay');
        $ttr      = $input->getArgument('ttr');
        $name     = $input->getArgument('pheanstalk');

        $pheanstalk = $this->getPheanstalk($name);

        $pheanstalk->useTube($tube);
        $jobId = $pheanstalk->put($data, $priority, $delay, $ttr);

        $output->writeln('Pheanstalk: <info>'.$name.'</info>');
        $output->writeln('New job on tube <info>'.$tube.'</info> with id <info>'.$jobId.'</info>.');

        return 0;
    }
}
