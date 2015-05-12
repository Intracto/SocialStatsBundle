<?php

namespace SocialStatsBundle\Service\Logging\Facebook;

use Doctrine\ORM\EntityManager;
use SocialStatsBundle\Entity\Log;
use SocialStatsBundle\Service\Logging\LoggerInterface;

class PageLikeCountLogger implements LoggerInterface{

    const LOG_SOURCE = Log::SOURCE_FACEBOOK;
    const LOG_TYPE = Log::TYPE_LIKES;

    /** @var \Doctrine\ORM\EntityManager $em */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $account
     * @param int $content
     *
     * @return string|Log
     */
    public function log($account, $content)
    {
        try {
            $log = new Log(self::LOG_SOURCE, $account, self::LOG_TYPE, $content);

            $this->em->persist($log);
            $this->em->flush();

            return $log;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
} 