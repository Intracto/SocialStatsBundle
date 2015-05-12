<?php

namespace SocialStatsBundle\Command\Facebook;

use SocialStatsBundle\Entity\Log;
use SocialStatsBundle\Service\API\FacebookAPIService;
use SocialStatsBundle\Service\Logging\Facebook\PageLikeCountLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogPageLikesCountCommand extends Command
{

    /** @var FacebookAPIService $facebookAPI */
    private $facebookAPI;

    /** @var PageLikeCountLogger $pageLikeCountLogger */
    private $pageLikeCountLogger;

    /** @var array $pages */
    private $pages;

    public function __construct(
      FacebookAPIService $facebookAPI,
      PageLikeCountLogger $pageLikeCountLogger,
      array $pages
    ) {
        $this->facebookAPI = $facebookAPI;
        $this->pageLikeCountLogger = $pageLikeCountLogger;
        $this->pages = $pages;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setName('socialstats:log:facebook:page-likes-count')
          ->setDescription('Log the amount of likes on Facebook for all page names in parameters.');
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
        if (count($this->pages) == 0) {
            $notice = 'No Facebook pages found. Please provide at least one page name in parameters.yml at key facebook.pages';
            $output->writeln(sprintf('<info>%s</info>', $notice));
        }

        foreach ($this->pages as $page) {
            $output->writeln(sprintf('<info>Started logging the like count for Facebook page %s</info>', $page));

            $likeCount = $this->facebookAPI->getLikeCountForPage($page);
            $result = $this->pageLikeCountLogger->log($page, $likeCount);

            if ($result instanceof Log) {
                $output->writeln(sprintf('<info>Finished logging for Facebook page %s</info>', $page));
            } else {
                $output->writeln(sprintf('<error>Logging failed for Facebook page %s</error>', $page));
            }
        }
    }
} 