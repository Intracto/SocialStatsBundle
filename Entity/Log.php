<?php

namespace SocialStatsBundle\Entity;

class Log
{
    const SOURCE_TWITTER = 'Twitter';
    const SOURCE_FACEBOOK = 'Facebook';
    const SOURCE_GA = 'Google Analytics';
    const TYPE_LIKES = 'likes';
    const TYPE_FOLLOWER_COUNT = 'follower_count';
    const TYPE_RETWEETS = 'retweets';

    /** @var int $id */
    private $id;

    /** @var \DateTime $timestamp */
    private $timestamp;

    /** @var string $source Has to be a constant defined e.g. Twitter */
    private $source;

    /** @var string $account */
    private $account;

    /** @var string $type Has to be a constant defined e.g. likes, retweets, followers */
    private $type;

    /** @var int $content e.g. 2695 (likes) */
    private $content;

    public function __construct($source, $account, $type, $content)
    {
        $this->timestamp = new \DateTime();
        $this->source = $source;
        $this->account = $account;
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getContent()
    {
        return $this->content;
    }
} 