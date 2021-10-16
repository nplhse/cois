<?php

namespace App\Controller;

use App\Entity\Allocation;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
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
    #[Route('/allocations/', name: 'app_allocation_index', methods: ['GET'])]
    public function index_new(Request $request, AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository): Response
    {
        $hospital = $request->query->get('hospital');
        $supplyArea = $request->query->get('supplyArea');
        $dispatchArea = $request->query->get('dispatchArea');
        $startDate = $request->query->get('start');
        $endDate = $request->query->get('end');
        $reqResus = $request->query->get('reqResus');
        $reqCath = $request->query->get('reqCath');
        $isCPR = $request->query->get('isCPR');
        $isVent = $request->query->get('isVent');
        $isShock = $request->query->get('isShock');
        $isWithDoc = $request->query->get('isWithDoc');
        $isPreg = $request->query->get('isPreg');
        $isWork = $request->query->get('isWork');
        $sortBy = $request->query->get('sortBy');
        $order = $request->query->get('order');
        $PZC = $request->query->get('pzc');
        $page = $request->query->get('page');

        $filters = [];
        $filters['search'] = $request->query->get('search');

        if ($hospital) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $filters['hospital'] = $hospital;
            } else {
                $filters['hospital'] = $this->getUser()->getHospital()->getId();
            }
        } else {
            $filters['hospital'] = null;
        }

        if ($supplyArea) {
            $filters['supplyArea'] = $supplyArea;
        } else {
            $filters['supplyArea'] = null;
        }

        if ($dispatchArea) {
            $filters['dispatchArea'] = $dispatchArea;
        } else {
            $filters['dispatchArea'] = null;
        }

        if ($startDate) {
            $filters['start'] = $startDate;
        } else {
            $filters['start'] = null;
        }

        if ($endDate) {
            $filters['end'] = $endDate;
        } else {
            $filters['end'] = null;
        }

        if ($reqResus) {
            $filters['reqResus'] = $reqResus;
        } else {
            $filters['reqResus'] = false;
        }

        if ($reqCath) {
            $filters['reqCath'] = $reqCath;
        } else {
            $filters['reqCath'] = false;
        }

        if ($isCPR) {
            $filters['isCPR'] = $isCPR;
        } else {
            $filters['isCPR'] = false;
        }

        if ($isVent) {
            $filters['isVent'] = $isVent;
        } else {
            $filters['isVent'] = false;
        }

        if ($isShock) {
            $filters['isShock'] = $isShock;
        } else {
            $filters['isShock'] = false;
        }

        if ($isVent) {
            $filters['isVent'] = $isVent;
        } else {
            $filters['isVent'] = false;
        }

        if ($isWithDoc) {
            $filters['isWithDoc'] = $isWithDoc;
        } else {
            $filters['isWithDoc'] = false;
        }

        if ($isPreg) {
            $filters['isPreg'] = $isPreg;
        } else {
            $filters['isPreg'] = false;
        }

        if ($isWork) {
            $filters['isWork'] = $isWork;
        } else {
            $filters['isWork'] = false;
        }

        if ($sortBy) {
            $filters['sortBy'] = $sortBy;
        } else {
            $filters['sortBy'] = null;
        }

        if ($order) {
            $filters['order'] = $order;
        } else {
            $filters['order'] = null;
        }

        if ($PZC) {
            $filters['pzc'] = $PZC;
        } else {
            $filters['pzc'] = null;
        }

        if (empty($page)) {
            $offset = max(0, $request->query->getInt('offset', 0));
        } else {
            $offset = (int) $page * AllocationRepository::PAGINATOR_PER_PAGE;
            --$offset;
        }

        $paginator = $allocationRepository->getAllocationPaginator($offset, $filters);
        $count = count($paginator);

        if (0 == $count % 10) {
            $last = $count - 10;
        } else {
            $last = floor($count / 10) * 10;
        }

        //if (null != $offset) {
        //    $offset = $count - AllocationRepository::PAGINATOR_PER_PAGE;
        //    $paginator = $allocationRepository->getAllocationPaginator($offset, $filters);
        //}

        $pages = ceil($count / AllocationRepository::PAGINATOR_PER_PAGE);

        $currentPage = ceil(($offset - 1) / AllocationRepository::PAGINATOR_PER_PAGE) + 1;
        $previous = $offset - AllocationRepository::PAGINATOR_PER_PAGE;
        $next = min($count, $offset + AllocationRepository::PAGINATOR_PER_PAGE);

        $PZCs = $allocationRepository->getPZCs();

        $col = array_column($PZCs, 'PZC');
        array_multisort($col, SORT_ASC, $PZCs);

        return $this->render('allocation/index.html.twig', [
            'allocations' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'perPage' => AllocationRepository::PAGINATOR_PER_PAGE,
            'previous' => $previous,
            'next' => $next,
            'last' => $last,
            'pages' => $pages,
            'currentPage' => $currentPage,
            'hospitals' => $hospitalRepository->getHospitals(),
            'supplyAreas' => $hospitalRepository->getSupplyAreas(),
            'dispatchAreas' => $hospitalRepository->getDispatchAreas(),
            'PZCs' => $PZCs,
            'page' => $page,
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
}
