<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Allocation;
use App\Factory\AllocationFilterFactory;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Repository\AllocationRepository;
use App\Repository\ImportRepository;
use App\Service\Filters\AssignmentFilter;
use App\Service\Filters\DateFilter;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\HospitalOwnerFilter;
use App\Service\Filters\ImportFilter;
use App\Service\Filters\IndicationFilter;
use App\Service\Filters\InfectionFilter;
use App\Service\Filters\IsCPRFilter;
use App\Service\Filters\IsPregnantFilter;
use App\Service\Filters\IsShockFilter;
use App\Service\Filters\IsVentilatedFilter;
use App\Service\Filters\IsWithPhysicianFilter;
use App\Service\Filters\IsWorkAccidentFilter;
use App\Service\Filters\ModeOfTransportFilter;
use App\Service\Filters\OccasionFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\RequiresCathlabFilter;
use App\Service\Filters\RequiresResusFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\SpecialityDetailFilter;
use App\Service\Filters\SpecialityFilter;
use App\Service\Filters\StateFilter;
use App\Service\Filters\SupplyAreaFilter;
use App\Service\Filters\UrgencyFilter;
use App\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class AllocationController extends AbstractController
{
    public function __construct(
        private FilterService $filterService
    ) {
    }

    #[Route('/allocations/', name: 'app_allocation_index', methods: ['GET'])]
    public function index(Request $request, AllocationRepository $allocationRepository, AllocationFilterFactory $allocationFilterFactory, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($allocationFilterFactory->getFilters());

        $paginator = $allocationRepository->getAllocationPaginator($this->filterService);

        $allocationFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $allocationForm = $allocationFilterFactory->setAction($this->generateUrl('app_allocation_index'))->getForm();
        $allocationForm->handleRequest($request);

        $orderFilterFactory->setHiddenFields([
            HospitalFilter::Param => $this->filterService->getValue(HospitalFilter::Param),
            ImportFilter::Param => $this->filterService->getValue(ImportFilter::Param),
            DateFilter::Param => $this->filterService->getValue(DateFilter::Param),
            IndicationFilter::Param => $this->filterService->getValue(IndicationFilter::Param),
            AssignmentFilter::Param => $this->filterService->getValue(AssignmentFilter::Param),
            InfectionFilter::Param => $this->filterService->getValue(InfectionFilter::Param),
            ModeOfTransportFilter::Param => $this->filterService->getValue(ModeOfTransportFilter::Param),
            OccasionFilter::Param => $this->filterService->getValue(OccasionFilter::Param),
            RequiresResusFilter::Param => $this->filterService->getValue(RequiresResusFilter::Param),
            RequiresCathlabFilter::Param => $this->filterService->getValue(RequiresCathlabFilter::Param),
            SpecialityFilter::Param => $this->filterService->getValue(SpecialityFilter::Param),
            SpecialityDetailFilter::Param => $this->filterService->getValue(SpecialityDetailFilter::Param),
            UrgencyFilter::Param => $this->filterService->getValue(UrgencyFilter::Param),
            IsCPRFilter::Param => $this->filterService->getValue(IsCPRFilter::Param),
            IsPregnantFilter::Param => $this->filterService->getValue(IsPregnantFilter::Param),
            IsShockFilter::Param => $this->filterService->getValue(IsShockFilter::Param),
            IsVentilatedFilter::Param => $this->filterService->getValue(IsVentilatedFilter::Param),
            IsWithPhysicianFilter::Param => $this->filterService->getValue(IsWithPhysicianFilter::Param),
            IsWorkAccidentFilter::Param => $this->filterService->getValue(IsWorkAccidentFilter::Param),
            OwnHospitalFilter::Param => $this->filterService->getValue(OwnHospitalFilter::Param),
            HospitalOwnerFilter::Param => $this->filterService->getValue(HospitalOwnerFilter::Param),
            StateFilter::Param => $this->filterService->getValue(StateFilter::Param),
            SupplyAreaFilter::Param => $this->filterService->getValue(SupplyAreaFilter::Param),
            DispatchAreaFilter::Param => $this->filterService->getValue(DispatchAreaFilter::Param),
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(AllocationRepository::SORTABLE)->setAction($this->generateUrl('app_allocation_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_allocation_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('allocation/index.html.twig', [
            'allocations' => $paginator,
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'allocationForm' => $allocationForm,
            'filters' => $this->filterService->getFilterDto(),
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), AllocationRepository::PER_PAGE),
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
