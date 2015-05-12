<?php

namespace SocialStatsBundle\Service\Logging;

interface LoggerInterface {
    /**
     * @param $account
     * @param $content
     * @return mixed
     */
    public function log($account, $content);
} 