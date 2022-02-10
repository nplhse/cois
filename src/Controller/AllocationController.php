<?php

namespace App\Controller;

use App\Domain\Repository\DispatchAreaRepositoryInterface;
use App\Domain\Repository\StateRepositoryInterface;
use App\Domain\Repository\SupplyAreaRepositoryInterface;
use App\Entity\Allocation;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\RequestParamService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class AllocationController extends AbstractController
{
    private StateRepositoryInterface $stateRepository;

    private SupplyAreaRepositoryInterface $supplyAreaRepository;

    private DispatchAreaRepositoryInterface $dispatchAreaRepository;

    public function __construct(StateRepositoryInterface $stateRepository, SupplyAreaRepositoryInterface $supplyAreaRepository, DispatchAreaRepositoryInterface $dispatchAreaRepository)
    {
        $this->stateRepository = $stateRepository;
        $this->supplyAreaRepository = $supplyAreaRepository;
        $this->dispatchAreaRepository = $dispatchAreaRepository;
    }

    #[Route('/allocations/', name: 'app_allocation_index', methods: ['GET'])]
    public function index_new(Request $request, AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository): Response
    {
        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['supplyArea'] = $paramService->getSupplyArea();
        $filters['dispatchArea'] = $paramService->getDispatchArea();
        $filters['assignment'] = $paramService->getAssignment();
        $filters['occasion'] = $paramService->getOccasion();
        $filters['modeOfTransport'] = $paramService->getTransport();
        $filters['pzc'] = $paramService->getPZC();
        $filters['urgency'] = $paramService->getUrgency();
        $filters['speciality'] = $paramService->getSpeciality();
        $filters['specialityDetail'] = $paramService->getSpecialityDetail();
        $filters['infection'] = $paramService->getInfection();

        $filters['reqResus'] = $paramService->getReqResus();
        $filters['reqCath'] = $paramService->getReqCath();
        $filters['isCPR'] = $paramService->getIsCPR();
        $filters['isVent'] = $paramService->getIsVentilated();
        $filters['isShock'] = $paramService->getIsShock();
        $filters['isWithDoc'] = $paramService->getIsWithDoctor();
        $filters['isPreg'] = $paramService->getIsPregnant();
        $filters['isWork'] = $paramService->getIsWorkAccident();

        $filters['startDate'] = $paramService->getStartDate();
        $filters['endDate'] = $paramService->getEndDate();
        $filters['sortBy'] = $paramService->getSortBy();
        $filters['orderBy'] = $paramService->getOrderBy();

        if ($paramService->getHospital()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $filters['hospital'] = $paramService->getHospital();
            } else {
                if ($paramService->getHospital() != $this->getUser()->getHospital()->getId()) {
                    $this->createAccessDeniedException('Cannot filter by this hospital.');
                }

                $filters['hospital'] = $paramService->getHospital();
            }
        } else {
            $filters['hospital'] = null;
        }

        $paginator = $allocationRepository->getAllocationPaginator($paramService->getPage(), $filters);

        return $this->render('allocation/index.html.twig', [
            'allocations' => $paginator,
            'filters' => $filters,
            'filterIsSet' => $paramService->isFilterIsSet(),
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), AllocationRepository::PAGINATOR_PER_PAGE),
            'hospitals' => $hospitalRepository->getHospitals(),
            'supplyAreas' => $this->supplyAreaRepository->findAll(),
            'dispatchAreas' => $this->dispatchAreaRepository->findAll(),
            'assignments' => $allocationRepository->getAllAssignments(),
            'occasions' => $allocationRepository->getAllOccasions(),
            'transports' => $allocationRepository->getAllTransportModes(),
            'urgencies' => $allocationRepository->getAllUrgencies(),
            'infections' => $allocationRepository->getAllInfections(),
            'PZCs' => $this->getAllPZCs($allocationRepository),
            'specialities' => $allocationRepository->getAllSpecialities(),
            'specialityDetails' => $allocationRepository->getAllSpecialityDetails(),
        ]);
    }

    #[Route('/allocations/{id}', name: 'app_allocation_show', methods: ['GET'])]
    public function show(Allocation $allocation, ImportRepository $importRepository): Response
    {
        $importUser = $allocation->getImport()->getUser();
        $importUser->getId();

        return $this->render('allocation/show.html.twig', [
            'allocation' => $allocation,
            'importUser' => $importUser,
        ]);
    }

    private function getAllPZCs(AllocationRepository $allocationRepository): array
    {
        $PZCs = $allocationRepository->getPZCs();

        $col = array_column($PZCs, 'PZC');
        array_multisort($col, SORT_ASC, $PZCs);

        return $PZCs;
    }
}
