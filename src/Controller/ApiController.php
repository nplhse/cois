<?php

namespace App\Controller;

use App\DataTransferObjects\GenderStatisticsDto;
use App\DataTransferObjects\TimeStatisticsDto;
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

        $results = [];

        foreach ($allocations->getItems() as $allocation) {
            $results[] = [
                'time' => $allocation->getTime(),
                'count' => $allocation->getCounter(),
            ];
        }

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
