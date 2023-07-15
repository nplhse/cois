<?php

declare(strict_types=1);

namespace App\Controller\Statistics;

use App\Factory\StatisticsFilterFactory;
use App\Query\AllocationUrgencyQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrgencyStatisticsController extends AbstractStatisticsController
{
    public function getQueryClass(): string
    {
        return AllocationUrgencyQuery::class;
    }

    #[Route('/statistics/urgency', name: 'app_statistics_urgency')]
    public function index(Request $request, StatisticsFilterFactory $filterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($filterFactory->getFilters());

        $filterForm = $filterFactory->setAction($this->generateUrl('app_statistics_urgency'))->getForm();
        $filterForm->handleRequest($request);

        $result = $this->getQuery()->execute($this->filterService);

        return $this->renderForm('statistics/urgency.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'results' => $result->getItems(),
            'data' => $this->generateChartData($result->getItems()),
            'filter_form' => $filterForm,
        ]);
    }

    public function generateChartData(array $data): array
    {
        $results = [];
        $total = count($data);

        foreach ($data as $allocation) {
            if ('0' === $allocation['urgency'] || '' === $allocation['urgency']) {
                continue;
            }

            $total += $allocation['counter'];
        }

        foreach ($data as $allocation) {
            if (0 === $allocation['urgency'] || '' === $allocation['urgency']) {
                continue;
            }

            $results[] = [
                'label' => 'SK'.$allocation['urgency'],
                'count' => $allocation['counter'],
                'percent' => $this->getValueInPercent($allocation['counter'], $total),
            ];
        }

        return $results;
    }
}
