<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @IsGranted("ROLE_USER")
 */
class VueStatisticsController extends AbstractController
{
    private StatisticsService $statistics;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statistics = $statisticsService;
    }

    #[Route('/vuestats', name: 'vue_stats')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        return $this->render('statistics/vue.html.twig');
    }

    #[Route('/vuestats/api/age.json', name: 'vue_api_age')]
    public function age_api(): JsonResponse
    {
        $age_stats = $this->statistics->generateAgeStats();

        $return = [];
        $male_ages = [];
        $female_ages = [];
        $other_ages = [];

        foreach ($age_stats->getMaleAges() as $key => $value)
        {
            $male_ages[$key] = $value;
        }
        $return['Male'] = $male_ages;

        foreach ($age_stats->getFemaleAges() as $key => $value)
        {
            $female_ages[$key] = $value;
        }
        $return['Female'] = $female_ages;

        foreach ($age_stats->getOtherAges() as $key => $value)
        {
            $other_ages[$key] = $value;
        }
        $return['Other'] = $other_ages;

        return $this->json($return);
    }

    #[Route('/vuestats/api/gender.json', name: 'vue_api_gender')]
    public function gender_api(): JsonResponse
    {
        $stats = $this->statistics->generateGenderStats();

        $return = [];
        $return['Male'] = $stats->getMaleCount();
        $return['Female'] = $stats->getFemaleCount();
        $return['Other'] = $stats->getOtherCount();

        return $this->json($return);
    }
}
