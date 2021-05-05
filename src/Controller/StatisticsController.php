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

        $sk_chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $sk_chart->setData([
            'labels' => ['SK1', 'SK2', 'SK3'],
            'datasets' => [
                [
                    'data' => $allocation_stats->getSK(),
                    'backgroundColor' => [
                        'rgba(226, 54, 54, 0.5)',
                        'rgba(226, 140, 54, 0.5)',
                        'rgba(54, 226, 54, 0.5)',
                    ],
                ],
            ],
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getRequiresResus());

        $resus_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $resus_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.5)',
                    ],
                ],
            ],
        ]);
        $resus_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getRequiresCathlab());

        $cath_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $cath_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.5)',
                    ],
                ],
            ],
        ]);
        $cath_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getIsCPR());

        $cpr_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $cpr_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.2)',
                    ],
                ],
            ],
        ]);
        $cpr_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getIsVentilated());

        $vent_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $vent_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.2)',
                    ],
                ],
            ],
        ]);
        $vent_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getIsShock());

        $shock_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $shock_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.2)',
                    ],
                ],
            ],
        ]);
        $shock_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getIsPregnant());

        $preg_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $preg_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.2)',
                    ],
                ],
            ],
        ]);
        $preg_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getIsWithPhysician());

        $phys_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $phys_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.5)',
                    ],
                ],
            ],
        ]);
        $phys_chart->setOptions([
            'legend' => false,
        ]);

        $chart = $this->buildDoughnutArray($allocation_stats->getIsWorkAccident());

        $work_chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $work_chart->setData([
            'labels' => $chart['labels'],
            'datasets' => [
                [
                    'data' => $chart['values'],
                    'backgroundColor' => [
                        'rgba(0, 0, 255, 0.2)',
                    ],
                ],
            ],
        ]);
        $work_chart->setOptions([
            'legend' => false,
        ]);

        $infections_labels = [];
        $infections_values = [];

        $infections = $allocation_stats->getIsInfectious();
        foreach ($infections as $key => $value) {
            array_push($infections_labels, $key);
            array_push($infections_values, $value);
        }

        $inf_chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $inf_chart->setData([
            'labels' => $infections_labels,
            'datasets' => [
                [
                    'data' => $infections_values,
                    'backgroundColor' => [
                        '#4E79A7',
                        '#F28E2B',
                        '#E15759',
                        '#BAB0AC',
                        '#59A14F',
                        '#EDC948',
                        '#B07AA1',
                        '#FF9DA7',
                        '#9C755F',
                        '#76B7B2',
                        '#7CD17D',
                        '#D7B5A6',
                        '#A0CBE8',
                    ],
                ],
            ],
        ]);

        return $this->render('statistics/allocations.html.twig', [
            'allocation_stats' => $allocation_stats,
            'items' => json_encode($allocation_stats->getRMIs()),
            'sk_chart' => $sk_chart,
            'resus_chart' => $resus_chart,
            'cath_chart' => $cath_chart,
            'cpr_chart' => $cpr_chart,
            'vent_chart' => $vent_chart,
            'shock_chart' => $shock_chart,
            'preg_chart' => $preg_chart,
            'phys_chart' => $phys_chart,
            'work_chart' => $work_chart,
            'inf_chart' => $inf_chart,
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
                    'backgroundColor' => [
                        '#4E79A7',
                        '#F28E2B',
                        '#E15759',
                        '#BAB0AC',
                        '#BAB0AC',
                        '#59A14F',
                        '#EDC948',
                        '#B07AA1',
                        '#FF9DA7',
                        '#9C755F',
                        '#76B7B2',
                        '#7CD17D',
                        '#D7B5A6',
                        '#A0CBE8',
                        '#F1CE63',
                        '#D37295',
                        '#E15759',
                    ],
                ],
            ],
        ]);

        return $this->render('statistics/specialities.html.twig', [
            'speciality_chart' => $speciality_chart,
        ]);
    }

    private function buildDoughnutArray(array $input): array
    {
        $result = [];
        $result['labels'] = [];
        $result['values'] = [];

        foreach ($input as $key => $value) {
            array_push($result['labels'], $key);
            array_push($result['values'], $value);
        }

        return $result;
    }
}
