<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatisticsController extends AbstractController
{
    private StatisticsService $statistics;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statistics = $statisticsService;
    }

    #[Route('/statistics', name: 'statistics_index')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $gender = $this->statistics->generateGenderStats();

        dump($gender);

        return $this->render('statistics/index.html.twig', [
            'gender' => $gender,
            'age' => $this->statistics->generateAgeStats(),
        ]);
    }
}
