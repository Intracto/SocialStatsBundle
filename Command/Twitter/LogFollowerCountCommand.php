<?php

namespace SocialStatsBundle\Command\Twitter;

use SocialStatsBundle\Entity\Log;
use SocialStatsBundle\Service\API\TwitterAPIService;
use SocialStatsBundle\Service\Logging\Twitter\FollowerCountLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogFollowerCountCommand extends Command
{

    /** @var TwitterAPIService $twitterAPI */
    private $twitterAPI;

    /** @var FollowerCountLogger $followerCountLogger */
    private $followerCountLogger;

    /** @var array $userNames */
    private $userNames;

    public function __construct(
      TwitterAPIService $twitterAPI,
      FollowerCountLogger $followerCountLogger,
      array $userNames
    ) {
        $this->twitterAPI = $twitterAPI;
        $this->followerCountLogger = $followerCountLogger;
        $this->userNames = $userNames;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setName('socialstats:log:twitter:follower-count')
          ->setDescription('Log the amount of followers on Twitter for all usernames in parameters.');
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
        if (count($this->userNames) == 0) {
            $notice = 'No Twitter accounts found. Please provide at least one username in parameters.yml at key twitter.usernames';
            $output->writeln(sprintf('<info>%s</info>', $notice));
        }

        foreach ($this->userNames as $user) {
            $output->writeln(sprintf('<info>Started logging the follower count for Twitter account %s</info>', $user));

            $followerCount = $this->twitterAPI->getFollowerCountForUser($user);
            $result = $this->followerCountLogger->log($user, $followerCount);

            if ($result instanceof Log) {
                $output->writeln(sprintf('<info>Finished logging for Twitter account %s</info>', $user));
            } else {
                $output->writeln(sprintf('<error>Logging failed for Twitter account %s</error>', $user));
            }
        }
    }
} 