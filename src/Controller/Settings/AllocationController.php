<?php

namespace App\Controller\Settings;

use App\Entity\Allocation;
use App\Factory\PaginationFactory;
use App\Repository\AllocationRepository;
use App\Service\Filters\AllocationFilterSet;
use App\Service\Filters\AssignmentFilter;
use App\Service\Filters\DateFilter;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\HospitalOwnerFilter;
use App\Service\Filters\IndicationFilter;
use App\Service\Filters\InfectionFilter;
use App\Service\Filters\IsCPRFilter;
use App\Service\Filters\IsPregnantFilter;
use App\Service\Filters\IsShockFilter;
use App\Service\Filters\IsVentilatedFilter;
use App\Service\Filters\IsWithPhysicianFilter;
use App\Service\Filters\IsWorkAccidentFilter;
use App\Service\Filters\LocationFilter;
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

#[IsGranted('ROLE_ADMIN')]
#[Route('/settings/allocation')]
class AllocationController extends AbstractController
{
    private FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    #[Route('/', name: 'app_settings_allocation_index', methods: ['GET'])]
    public function index(Request $request, AllocationRepository $allocationRepository): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters([AllocationFilterSet::Param, HospitalFilter::Param, DateFilter::Param, IndicationFilter::Param, AssignmentFilter::Param, IndicationFilter::Param, InfectionFilter::Param, ModeOfTransportFilter::Param, OccasionFilter::Param, RequiresResusFilter::Param, RequiresCathlabFilter::Param, SpecialityFilter::Param, SpecialityDetailFilter::Param, UrgencyFilter::Param, IsCPRFilter::Param, IsPregnantFilter::Param, IsShockFilter::Param, IsVentilatedFilter::Param, IsWithPhysicianFilter::Param, IsWorkAccidentFilter::Param, StateFilter::Param, DispatchAreaFilter::Param, SupplyAreaFilter::Param, OwnHospitalFilter::Param, HospitalOwnerFilter::Param, PageFilter::Param, SearchFilter::Param, OrderFilter::Param]);

        $paginator = $allocationRepository->getAllocationPaginator($this->filterService);

        $args = [
            'action' => $this->generateUrl('app_settings_allocation_index'),
            'method' => 'GET',
        ];

        $allocationArguments = [
            'hidden' => [
                SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
                OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
            ],
        ];

        $allocationForm = $this->filterService->buildForm(AllocationFilterSet::Param, array_merge($allocationArguments, $args));
        $allocationForm->handleRequest($request);

        $sortArguments = [
            'sortable' => AllocationRepository::SORTABLE,
            'hidden' => [
                LocationFilter::Param => $this->filterService->getValue(LocationFilter::Param),
                StateFilter::Param => $this->filterService->getValue(StateFilter::Param),
                SupplyAreaFilter::Param => $this->filterService->getValue(SupplyAreaFilter::Param),
                DispatchAreaFilter::Param => $this->filterService->getValue(DispatchAreaFilter::Param),
                SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            ],
        ];

        $sortForm = $this->filterService->buildForm(OrderFilter::Param, array_merge($sortArguments, $args));
        $sortForm->handleRequest($request);

        $searchArguments = [
            'hidden' => [
                OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
            ],
        ];

        $searchForm = $this->filterService->buildForm(SearchFilter::Param, array_merge($searchArguments, $args));
        $searchForm->handleRequest($request);

        return $this->renderForm('settings/allocation/index.html.twig', [
            'allocations' => $paginator,
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'allocationForm' => $allocationForm,
            'filters' => $this->filterService->getFilterDto(),
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), AllocationRepository::PER_PAGE),
        ]);
    }

    #[Route('/{id}', name: 'app_settings_allocation_show', methods: ['GET'])]
    public function show(Allocation $allocation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('settings/allocation/show.html.twig', [
            'allocation' => $allocation,
        ]);
    }
}
