<?php

namespace App\Controller;

use App\DataTransferObjects\GenderStats;
use App\DataTransferObjects\TimeStats;
use App\Query\AllocationQuery;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private HospitalRepository $hospitalRepository;

    private AllocationQuery $allocationQuery;

    public function __construct(HospitalRepository $hospitalRepository, AllocationQuery $allocationQuery)
    {
        $this->hospitalRepository = $hospitalRepository;
        $this->allocationQuery = $allocationQuery;
    }

    #[Route('/api/gender.json', name: 'app_data_gender')]
    public function gender(Request $request): Response
    {
        $paramService = new RequestParamService($request);
        $hospitalId = $paramService->hospital;

        if (isset($hospitalId) && !empty($hospitalId)) {
            $hospital = $this->hospitalRepository->findById($hospitalId);

            if ($this->isGranted('viewStats', $hospital)) {
                $this->allocationQuery->filterByHospital($hospital);
            } else {
                throw $this->createAccessDeniedException('Cannot access this resource.');
            }
        }

        $this->allocationQuery->groupBy('gender');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(GenderStats::class);

        $results = [];
        $total = 0;

        foreach ($allocations->getItems() as $allocation) {
            $total = $total + $allocation->getCounter();
        }

        foreach ($allocations->getItems() as $allocation) {
            if ('M' == $allocation->getGender()) {
                $gender = 'male';
            } elseif ('W' == $allocation->getGender()) {
                $gender = 'female';
            } else {
                $gender = 'other';
            }

            $results[] = [
                'label' => $gender,
                'count' => $allocation->getCounter(),
                'percent' => number_format(round(($allocation->getCounter() / $total) * 100, 1), 2).'%',
            ];
        }

        return new JsonResponse($results);
    }

    #[Route('/api/times.json', name: 'app_data_time')]
    public function times(Request $request): Response
    {
        $paramService = new RequestParamService($request);
        $hospitalId = $paramService->hospital;

        if (isset($hospitalId) && !empty($hospitalId)) {
            $hospital = $this->hospitalRepository->findById($hospitalId);

            if ($this->isGranted('viewStats', $hospital)) {
                $this->allocationQuery->filterByHospital($hospital);
            } else {
                throw $this->createAccessDeniedException('Cannot access this resource.');
            }
        }

        $this->allocationQuery->groupBy('times');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(TimeStats::class);

        $results = [];

        foreach ($allocations->getItems() as $allocation) {
            $results[] = [
                'time' => $allocation->getTime(),
                'count' => $allocation->getCounter(),
            ];
        }

        return new JsonResponse($results);
    }
}
