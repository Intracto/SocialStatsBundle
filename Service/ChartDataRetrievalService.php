<?php

namespace SocialStatsBundle\Service;

use Doctrine\DBAL\Connection;

class ChartDataRetrievalService {

    const DEFAULT_TIME_FORMAT = 'Y-m-d H:i:s';

    /** @var Connection $connection */
    private $connection;

    /** @var string $source */
    private $source;

    /** @var string $type */
    private $type;

    /** @var array $accounts */
    private $accounts;

    /** @var \DateTime $startDate */
    private $startDate;

    /** @var \DateTime $endDate */
    private $endDate;

    /** @var int $numIntervals */
    private $numIntervals;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getChartData()
    {
        $chartData = [];

        foreach ($this->accounts as $account) {
            $chartData[$account] = $this->getData($account);
        }

        if ($this->numIntervals !== null) {
            $result = [];

            foreach ($chartData as $account => $accountData) {
                $result[$account] = [];
                //We need to know how many records we have to group together, in order to get a maximum of 50 results.
                $step = (int) (count($accountData) / $this->numIntervals);

                for ($i = 0; $i < count($accountData); $i+=$step) {
                    $result[$account][] = $accountData[$i];
                }
            }

            return $result;
        }

        return $chartData;
    }

    private function getData($account)
    {
        $query = 'SELECT s.id, UNIX_TIMESTAMP(s.timestamp) as timestamp, s.source, s.type, s.account, s.content FROM social_stats_log as s
        WHERE s.source = :source AND s.type = :log_type AND s.account = :account';

        if ($this->startDate) {
            $query .= 'AND s.timestamp >= ' . $this->startDate->format(self::DEFAULT_TIME_FORMAT);
        }

        if ($this->endDate) {
            $query .= 'AND s.timestamp <= ' . $this->endDate->format(self::DEFAULT_TIME_FORMAT);
        }

        $statement = $this->connection->prepare($query);
        $statement->bindParam('source', $this->source);
        $statement->bindParam('log_type', $this->type);
        $statement->bindParam('account', $account);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    public function addAccount($account)
    {
        $this->accounts[] = $account;
    }

    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    public function setNumIntervals($numIntervals)
    {
        $this->numIntervals = $numIntervals;
    }
} 