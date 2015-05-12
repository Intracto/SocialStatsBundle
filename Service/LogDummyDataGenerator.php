<?php

namespace SocialStatsBundle\Service;

use Doctrine\DBAL\Connection;
use SocialStatsBundle\Entity\Log;

class LogDummyDataGenerator
{
    private $twitterTypes = array(Log::TYPE_FOLLOWER_COUNT);
    private $facebookTypes = array(Log::TYPE_LIKES);

    private $facebookPages;
    private $twitterUsernames;

    private $numLogsPerSource;

    /** @var \Doctrine\DBAL\Connection $connection */
    private $connection;

    public function __construct(Connection $connection, $facebookPages, $twitterUsernames)
    {
        $this->connection = $connection;
        $this->facebookPages = $facebookPages;
        $this->twitterUsernames = $twitterUsernames;
    }

    public function generate(
      $numLogsPerSource = 100,
      $sources = array(Log::SOURCE_FACEBOOK, Log::SOURCE_TWITTER)
    ) {
        //Max 500 records
        if ($numLogsPerSource > 500) {
            $numLogsPerSource = 500;
        }

        $this->numLogsPerSource = $numLogsPerSource;

        foreach ($sources as $source) {
            switch ($source) {
                case Log::SOURCE_FACEBOOK:
                    $this->generateForSource($source, $this->facebookPages, $this->facebookTypes);
                    break;

                case Log::SOURCE_TWITTER:
                    $this->generateForSource($source, $this->twitterUsernames, $this->twitterTypes);
                    break;
            }
        }
    }

    private function generateForSource($source, array $accounts, array $types)
    {
        foreach ($accounts as $account) {
            foreach ($types as $type) {
                $initialTimestamp = $this->getDate($source, $type, $account);

                for ($i = 0; $i < $this->numLogsPerSource; $i++) {
                    $interval = new \DateInterval('PT12H');
                    $timestamp = $initialTimestamp->add($interval);

                    $latestLog = $this->getLatestLog($source, $type, $account);

                    if ($latestLog !== false && array_key_exists('content', $latestLog)) {
                        $content = $latestLog['content'];
                    } else {
                        $content = mt_rand(0, 2000);
                    }

                    $addOrSub = mt_rand(0, 1);
                    $contentAddition = mt_rand(0, 50);

                    switch ($addOrSub) {
                        case 0:
                            if ($content > $contentAddition) {
                                $content -= $contentAddition;
                            }
                            break;
                        case 1:
                            $content += $contentAddition;
                            break;
                    }

                    $this->createLog($timestamp, $source, $account, $type, $content);
                }
            }
        }
    }

    private function createLog(\DateTime $timestamp, $source, $account, $type, $content)
    {
        $query = "INSERT INTO social_stats_log (timestamp, source, account, type, content)
        VALUES(:timestamp, :source, :account, :type, :content)
        ";

        $statement = $this->connection->prepare($query);

        $format = $timestamp->format('Y-m-d H:i:s');

        $statement->bindParam('timestamp', $format);
        $statement->bindParam('source', $source);
        $statement->bindParam('account', $account);
        $statement->bindParam('type', $type);
        $statement->bindParam('content', $content);

        return $statement->execute();
    }

    /**
     * Function to ensure we log 2 days from the latest log entry.
     * We do this to prevent we have duplicate log entries when the user executes the generator more than once.
     *
     * @param string $source
     * @param string $type
     * @param string $account
     *
     * @return \DateTime
     */
    private function getDate($source, $type, $account)
    {
        $result = $this->getLatestLog($source, $type, $account);

        if ($result !== false && array_key_exists('timestamp', $result)) {
            $latestLogDate = \DateTime::createFromFormat('Y-m-d H:i:s', $result['timestamp']);

            return $latestLogDate;
        }

        return new \DateTime();
    }

    /**
     * Function that fetches the latest log from the database for given params.
     *
     * @param $source
     * @param $type
     * @param $account
     *
     * @return bool|array
     */
    private function getLatestLog($source, $type, $account)
    {
        $query = 'SELECT * FROM social_stats_log as s
        WHERE s.source = :source AND s.type = :log_type AND s.account = :account
        ORDER BY s.timestamp DESC
        LIMIT 1';

        $statement = $this->connection->prepare($query);
        $statement->bindParam('source', $source);
        $statement->bindParam('log_type', $type);
        $statement->bindParam('account', $account);
        $statement->execute();
        $result = $statement->fetch();

        return $result;
    }
}