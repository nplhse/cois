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
            'labels' => $this->statistics->getScaleForXAxis($age_stats->getMaxAge(), 1),
            'datasets' => [
                [
                    'label' => 'Total male count by age',
                    'data' => $age_stats->getMaleAges(),
                    'borderColor' => 'rgba(54, 162, 235, 0.2)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'stacked' => true,
                ],
                [
                    'label' => 'Total female count by age',
                    'data' => $age_stats->getFemaleAges(),
                    'borderColor' => 'rgba(255, 99, 132, 0.2)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'stacked' => true,
                ],
                [
                    'label' => 'Total other count by age',
                    'data' => $age_stats->getOtherAges(),
                    'borderColor' => 'rgba(255, 206, 86, 0.2)',
                    'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    'stacked' => true,
                ],
            ],
        ]);
        $age_chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['stacked' => true],
                    ['ticks' => [
                        'beginAtZero' => true,
                    ]],
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

    #[Route('/statistics/times', name: 'statistics_times')]
    public function times(ChartBuilderInterface $chartBuilder): Response
    {
        $time_stats = $this->statistics->generateTimeStats();

        $time_of_day_chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $time_of_day_chart->setData([
            'labels' => $this->statistics->getScaleForXAxis(23, 1),
            'datasets' => [
                [
                    'label' => 'Total count by hours of the day',
                    'data' => $time_stats->getTimesOfDay(),
                ],
            ],
        ]);
        $time_of_day_chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['stacked' => false],
                    ['ticks' => [
                        'beginAtZero' => true,
                    ]],
                ],
            ],
        ]);

        $weekday_chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $weekday_chart->setData([
            'labels' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'datasets' => [
                [
                    'label' => 'Total count by weekdays',
                    'data' => $time_stats->getWeekdays(),
                ],
            ],
        ]);
        $weekday_chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => [
                        'beginAtZero' => true,
                    ]],
                ],
            ],
        ]);

        return $this->render('statistics/times.html.twig', [
            'time_stats' => $time_stats,
            'time_of_day_chart' => $time_of_day_chart,
            'weekday_chart' => $weekday_chart,
        ]);
    }

    #[Route('/statistics/allocations', name: 'statistics_allocations')]
    public function allocations(ChartBuilderInterface $chartBuilder): Response
    {
        $allocation_stats = $this->statistics->generateAllocationStats();

        $speciality_labels = [];
        $speciality_values = [];

        $specialities = $allocation_stats->getSpecialities();
        foreach ($specialities as $key => $value) {
            array_push($speciality_labels, $key);
            array_push($speciality_values, $value);
        }

        $speciality_chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $speciality_chart->setData([
            'labels' => $speciality_labels,
            'datasets' => [
                [
                    'data' => $speciality_values,
                ],
            ],
        ]);

        return $this->render('statistics/allocations.html.twig', [
            'allocation_stats' => $allocation_stats,
            'speciality_chart' => $speciality_chart,
        ]);
    }

    #[Route('/statistics/specialities', name: 'statistics_specialities')]
    public function specialities(ChartBuilderInterface $chartBuilder): Response
    {
        $allocation_stats = $this->statistics->generateAllocationStats();

        $speciality_labels = [];
        $speciality_values = [];

        $specialities = $allocation_stats->getSpecialities();

        foreach ($specialities as $key => $value) {
            array_push($speciality_labels, $key);
            array_push($speciality_values, $value);
        }

        $speciality_chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $speciality_chart->setData([
            'labels' => $speciality_labels,
            'datasets' => [
                [
                    'data' => $speciality_values,
                ],
            ],
        ]);

        $specialityDetail_labels = [];
        $specialityDetail_values = [];

        $specialityDetails = $allocation_stats->getSpecialityDetails();

        foreach ($specialityDetails as $key => $value) {
            array_push($specialityDetail_labels, $key);
            array_push($specialityDetail_values, $value);
        }

        $specialityDetail_chart = $chartBuilder->createChart(Chart::TYPE_POLAR_AREA);
        $specialityDetail_chart->setData([
            'labels' => $specialityDetail_labels,
            'datasets' => [
                [
                    'data' => $specialityDetail_values,
                ],
            ],
        ]);

        $specialityDetail_chart->setOptions([
            'indexAxis' => 'yAxis',
        ]);

        return $this->render('statistics/specialities.html.twig', [
            'speciality_chart' => $speciality_chart,
            'specialityDetail_chart' => $specialityDetail_chart,
        ]);
    }
}
