<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @IsGranted("ROLE_USER")
 */
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

        $age_stats = $this->statistics->generateAgeStats();

        $age_chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $age_chart->setData([
            'datasets' => [
                [
                    'data' => $age_stats->getAges(),
                ],
            ],
        ]);
        $age_chart->setOptions([
            'scales' => [
                'x' => [
                    ['ticks' => ['min' => 0, 'max' => $age_stats->getMaxAge()]],
                ],
            ],
        ]);

        return $this->render('statistics/index.html.twig', [
            'age' => $age_stats,
            'age_chart' => $age_chart,
            'gender' => $gender_stats,
            'gender_chart' => $gender_chart,
        ]);
    }
}
