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
    /**
     * @Route("/{_locale<%app.supported_locales%>}/allocations/", name="allocation_index")
     */
    public function index(HospitalRepository $hospitalRepository): Response
    {
        $hospitals = $hospitalRepository->findAll();

        $hospitalList = [];
        $hospitalLink = [];

        foreach ($hospitals as $hospital) {
            $hospitalList['/api/hospitals/'.$hospital->getId()] = $hospital->getName();
            $hospitalLink['/api/hospitals/'.$hospital->getId()] = $this->generateUrl('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('allocation/index.html.twig', [
            'hospitals' => json_encode($hospitalList),
            'hospital_links' => json_encode($hospitalLink),
        ]);
    }

    #[Route('/_new', name: 'app_allocation_index', methods: ['GET'])]
    public function index_new(Request $request, AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository): Response
    {
        $hospital = $request->query->get('hospital');
        $supplyArea = $request->query->get('supplyArea');
        $dispatchArea = $request->query->get('dispatchArea');

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

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $allocationRepository->getAllocationPaginator($offset, $filters);

        return $this->render('allocation/index_new.html.twig', [
            'allocations' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'perPage' => AllocationRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - AllocationRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AllocationRepository::PAGINATOR_PER_PAGE),
            'hospitals' => $hospitalRepository->getHospitals(),
            'supplyAreas' => $hospitalRepository->getSupplyAreas(),
            'dispatchAreas' => $hospitalRepository->getDispatchAreas(),
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/allocations/{id}", name="allocation_show")
     */
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
