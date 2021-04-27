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
        $gender_stats = $this->statistics->generateGenderStats();
        $gender_chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $gender_chart->setData([
            'labels' => ['Male', 'Female', 'Other'],
            'datasets' => [
                [
                    'data' => [$gender_stats->getMaleCount(), $gender_stats->getFemaleCount(), $gender_stats->getOtherCount()],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                    ],
                ],
            ],
        ]);

        return $this->render('statistics/index.html.twig', [
            'gender' => $gender_stats,
            'gender_chart' => $gender_chart,
        ]);
    }
}
