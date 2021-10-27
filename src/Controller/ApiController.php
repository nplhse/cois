<?php

namespace App\Controller;

use App\DataTransferObjects\DayStatisticsDto;
use App\DataTransferObjects\GenderStatisticsDto;
use App\DataTransferObjects\PZCStatisticsDto;
use App\DataTransferObjects\TimeStatisticsDto;
use App\DataTransferObjects\UrgencyStatisticsDto;
use App\Query\AllocationQuery;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private HospitalRepository $hospitalRepository;

    private AllocationQuery $allocationQuery;

    private StatisticsService $statisticsService;

    public function __construct(HospitalRepository $hospitalRepository, AllocationQuery $allocationQuery, StatisticsService $statisticsService)
    {
        $this->hospitalRepository = $hospitalRepository;
        $this->allocationQuery = $allocationQuery;
        $this->statisticsService = $statisticsService;
    }

    #[Route('/api/days.json', name: 'app_data_days')]
    public function days(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('days');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(DayStatisticsDto::class);

        $results = $this->statisticsService->generateDayResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/gender.json', name: 'app_data_gender')]
    public function gender(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('gender');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(GenderStatisticsDto::class);

        $results = $this->statisticsService->generateGenderResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/times.json', name: 'app_data_time')]
    public function times(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('times');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(TimeStatisticsDto::class);

        $results = $this->statisticsService->generateTimeResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/pzc.json', name: 'app_data_pzc')]
    public function pzc(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('pzc');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(PZCStatisticsDto::class);

        $results = $this->statisticsService->generatePZCResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/urgency.json', name: 'app_data_urgency')]
    public function urgency(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('urgency');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(UrgencyStatisticsDto::class);

        $results = $this->statisticsService->generateUrgencyResults($allocations);

        return new JsonResponse($results);
    }

    private function filterByHospital(Request $request): void
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                $hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $hospital)) {
                    $this->allocationQuery->filterByHospital($hospital);
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }
    }
}
