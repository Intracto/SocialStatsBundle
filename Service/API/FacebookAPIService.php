<?php

namespace SocialStatsBundle\Service\API;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use GuzzleHttp\Client;

class FacebookAPIService
{
    const BASE_URL = 'https://graph.facebook.com/v2.3/';

    /** @var FacebookSession $facebookSession */
    private $facebookSession;

    /** @var \GuzzleHttp\Client $client */
    private $client;

    /** @var string $appId */
    private $appId;

    /** @var string $apiSecret */
    private $apiSecret;

    /** @var string $accessToken */
    private $accessToken;

    public function __construct(Client $guzzleClient, $appId, $apiSecret)
    {
        $this->client = $guzzleClient;
        $this->appId = $appId;
        $this->apiSecret = $apiSecret;

        FacebookSession::setDefaultApplication($this->appId, $this->apiSecret);
    }

    public function getLikeCountForPage($page)
    {
        $data = $this->getDataForPage($page);

        return $data['likes'];
    }

    private function getDataForPage($page)
    {
        try {
            $request = new FacebookRequest($this->getFacebookSession(), 'GET', '/' . $page);
            $response = $request->execute();
            $graphObject = $response->getGraphObject();

            /** @var GraphObject $data */
            $data = $graphObject->asArray();

            return $data;
        } catch (\Exception $e) {
            return 'Something went wrong requesting the data for page ' . $page . ' : ' . $e->getMessage();
        }
    }

    private function retrieveAccessToken()
    {
        $query = [
          'client_id' => $this->appId,
          'client_secret' => $this->apiSecret,
          'grant_type' => 'client_credentials'
        ];

        try {
            $response = $this->client->get(self::BASE_URL . 'oauth/access_token', ['query' => $query]);

            if ($response->getStatusCode() == 200) {
                $response = $response->json();
                return $response['access_token'];
            }

            return 'Something went wrong authenticating the application with Facebook.';
        } catch (\Exception $e) {
            return 'Something went wrong authenticating the application with Facebook : ' . $e->getMessage();
        }

    }

    private function getAccessToken()
    {
        if ($this->accessToken === null) {
            $this->accessToken = $this->retrieveAccessToken();
        }

        return $this->accessToken;
    }

    private function getFacebookSession()
    {
        if ($this->facebookSession === null) {
            $this->facebookSession = new FacebookSession($this->getAccessToken());
        }

        return $this->facebookSession;
    }
} 