<?php

namespace App\Controller;

use App\DataTransferObjects\GenderStats;
use App\Query\AllocationQuery;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private AllocationRepository $allocationRepository;

    private HospitalRepository $hospitalRepository;

    private AllocationQuery $allocationQuery;

    public function __construct(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository, AllocationQuery $allocationQuery)
    {
        $this->allocationRepository = $allocationRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->allocationQuery = $allocationQuery;
    }

    #[Route('/api/gender.json', name: 'app_data_gender')]
    public function gender(Request $request): Response
    {
        $hospitalId = $request->query->get('hospital');

        if (isset($hospitalId) && !empty($hospitalId)) {
            $hospital = $this->hospitalRepository->findOneById($hospitalId);

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
                'caption' => $gender,
                'count' => $allocation->getCounter(),
                'percent' => round(($allocation->getCounter() / $total) * 100, 1),
            ];
        }

        return new JsonResponse($results);
    }
}
