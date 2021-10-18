<?php

namespace App\Controller;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
    private AllocationRepository $allocationRepository;

    private HospitalRepository $hospitalRepository;

    public function __construct(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository)
    {
        $this->allocationRepository = $allocationRepository;
        $this->hospitalRepository = $hospitalRepository;
    }

    #[Route('/data/gender.json', name: 'app_data_gender')]
    public function gender(Request $request): Response
    {
        $hospitalId = $request->query->get('hospital');

        if (empty($hospitalId)) {
            $data = $this->allocationRepository->countAllocationsByGender();
        } else {
            $hospital = $this->hospitalRepository->findOneById($hospitalId);

            if ($this->isGranted('viewStats', $hospital)) {
                $data = $this->allocationRepository->countAllocationsByGender($hospital);
            } else {
                throw $this->createAccessDeniedException('Cannot access this resource.');
            }
        }

        $stats = [];
        $stats['other'] = 0;

        foreach ($data as $item) {
            if ('M' === $item['gender']) {
                $stats['male'] = $item['counter'];
            }

            if ('W' === $item['gender']) {
                $stats['female'] = $item['counter'];
            }

            if ('D' === $item['gender']) {
                $stats['other'] = $item['counter'];
            }
        }

        $response = new JsonResponse([
            'male' => $stats['male'],
            'female' => $stats['female'],
            'other' => $stats['other'],
        ]);

        return $response;
    }
}
