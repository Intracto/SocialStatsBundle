<?php

namespace SocialStatsBundle\Command;

use SocialStatsBundle\Service\LogDummyDataGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LogDummyDataGenerateCommand extends Command
{

    /** @var LogDummyDataGenerator $generator */
    private $generator;

    public function __construct(LogDummyDataGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setName('socialstats:generator:log-dummy-data')
          ->setDescription('Log the amount of followers on Twitter for all usernames in parameters.')
          ->addArgument(
            'sources',
            InputArgument::IS_ARRAY,
            'What should be the source of the dummy log? E.g. Facebook'
          )
          ->addOption(
            'quantity',
            null,
            InputOption::VALUE_REQUIRED,
            'How much dummy data do you need for each source and type?',
            100
          );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $quantity = $input->getOption('quantity');

        $output->writeln(sprintf('<info>Started generating ' . $quantity . ' logs for each source and type..</info>'));

        try {
            if ($sources = $input->getArgument('sources')) {
                $this->generator->generate($quantity, $sources);
            } else {
                $this->generator->generate($quantity);
            }

            $output->writeln(sprintf('<info>Finished generating dummy data!</info>'));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error : %s</error>', $e->getMessage()));
        }
    }
} 