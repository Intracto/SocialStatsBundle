<?php

namespace SocialStatsBundle\Controller;

use SocialStatsBundle\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    public function facebookAction()
    {
        $facebookChartDataService = $this->get('chart.data_retriever.facebook');
        $facebookChartDataService->setType(Log::TYPE_LIKES);

        $viewData = [
            'accountData' => $facebookChartDataService->getChartData()
        ];

        return $this->render('SocialStatsBundle::facebook.html.twig', $viewData);
    }

    public function twitterAction()
    {
        $twitterChartDataService = $this->get('chart.data_retriever.twitter');
        $twitterChartDataService->setType(Log::TYPE_FOLLOWER_COUNT);

        $viewData = [
          'accountData' => $twitterChartDataService->getChartData()
        ];

        return $this->render('SocialStatsBundle::twitter.html.twig', $viewData);
    }
} 