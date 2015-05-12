<?php

namespace SocialStatsBundle\Service\API;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Exception\RequestException;

class TwitterAPIService
{

    const FOLLOWERS_IDS_PATH = 'followers/ids.json';
    const USER_SHOW_PATH = 'users/show.json';

    /** @var \GuzzleHttp\Client $client */
    private $client;

    /** @var string $username e.g. IntractoGroup */
    private $username;

    public function __construct(Client $guzzleClient, Oauth1 $oAuth)
    {
        $this->client = $guzzleClient;
        $this->client->getEmitter()->attach($oAuth);
    }

    public function getDataForUser($username = null)
    {
        $username = $this->resolveUsername($username);

        try {
            $query = ['screen_name' => $username];

            $response = $this->client->get(self::USER_SHOW_PATH, ['query' => $query]);

            if ($response->getStatusCode() == 200) {
                return $response->json();
            } else {
                throw new \Exception('Twitter API returned following error: '
                  . $response->getBody(), $response->getStatusCode());
            }

        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    public function getFollowerCountForUser($username = null)
    {
        $username = $this->resolveUsername($username);

        try {
            $userData = $this->getDataForUser($username);

            if (is_array($userData) && array_key_exists('followers_count', $userData)) {
                return $userData['followers_count'];
            } else {
                throw new \Exception('The data received from the Twitter API does not contain the followers count.');
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getFollowersIdsForUser($username = null)
    {
        $username = $this->resolveUsername($username);

        $batchSize = 4000;
        $cursor = -1;

        try {
            $query = [
              'screen_name' => $username,
              'cursor' => $cursor,
              'count' => $batchSize
            ];

            $followers = array();

            while ($cursor != 0) {
                $response = $this->client->get(self::FOLLOWERS_IDS_PATH, ['query' => $query]);

                $result = $response->json;

                $followers += $result['ids'];
                $cursor = $result['next_cursor'];
            }

            return $followers;
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    private function resolveUsername($username)
    {
        if ($username === null) {
            if ($this->username === null) {
                throw new \Exception('Please provide a username as a parameter or use the method setUsername.');
            }

            return $this->username;
        }

        return $username;
    }

} 