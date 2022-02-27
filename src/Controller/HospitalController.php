<?php

namespace App\Controller;

use App\Domain\Command\Hospital\CreateHospitalCommand;
use App\Domain\Command\Hospital\EditHospitalCommand;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\UserInterface;
use App\Entity\Hospital;
use App\Factory\HospitalFilterFactory;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Form\HospitalType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\LocationFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\SizeFilter;
use App\Service\Filters\StateFilter;
use App\Service\Filters\SupplyAreaFilter;
use App\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hospitals')]
class HospitalController extends AbstractController
{
    private FilterService $filterService;

    private MessageBusInterface $messageBus;

    public function __construct(FilterService $filterService, MessageBusInterface $messageBus)
    {
        $this->filterService = $filterService;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_hospital_index')]
    public function index(Request $request, HospitalRepository $hospitalRepository, HospitalFilterFactory $hospitalFilterFactory, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($hospitalFilterFactory->getFilters());

        $paginator = $hospitalRepository->getHospitalPaginator($this->filterService);

        $hospitalFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $hospitalForm = $hospitalFilterFactory->setAction($this->generateUrl('app_hospital_index'))->getForm();
        $hospitalForm->handleRequest($request);

        $orderFilterFactory->setHiddenFields([
            LocationFilter::Param => $this->filterService->getValue(LocationFilter::Param),
            SizeFilter::Param => $this->filterService->getValue(SizeFilter::Param),
            StateFilter::Param => $this->filterService->getValue(StateFilter::Param),
            SupplyAreaFilter::Param => $this->filterService->getValue(SupplyAreaFilter::Param),
            DispatchAreaFilter::Param => $this->filterService->getValue(DispatchAreaFilter::Param),
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(HospitalRepository::SORTABLE)->setAction($this->generateUrl('app_hospital_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_hospital_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('hospitals/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'hospitalForm' => $hospitalForm,
            'hospitals' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), HospitalRepository::PER_PAGE),
        ]);
    }

    #[Route(path: '/new', name: 'app_hospital_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('create_hospital', $this->getUser());

        $hospital = new Hospital();

        $form = $this->createForm(HospitalType::class, $hospital, [
            'backend' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $user = $hospital->getOwner();
            } else {
                /** @var UserInterface $user */
                $user = $this->getUser();
            }

            $command = new CreateHospitalCommand(
                $user,
                $hospital->getName(),
                $hospital->getAddress(),
                $hospital->getState(),
                $hospital->getDispatchArea(),
                $hospital->getSupplyArea(),
                $hospital->getLocation(),
                $hospital->getBeds(),
                $hospital->getSize()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            /** @var ?HospitalInterface $hospital */
            $hospital = $hospitalRepository->findOneByTriplet($command->getName(), $command->getLocation(), $command->getBeds());

            if (null === $hospital) {
                throw new \RuntimeException('Sorry, something went wrong. Please try again later.');
            }

            return $this->redirectToRoute('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('hospitals/new.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'app_hospital_edit', methods: ['GET', 'POST'])]
    public function edit(Hospital $hospital, Request $request, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $hospital);

        $form = $this->createForm(HospitalType::class, $hospital, [
            'backend' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        /** @var UserInterface $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new EditHospitalCommand(
                $hospital->getId(),
                $hospital->getOwner(),
                $hospital->getName(),
                $hospital->getAddress(),
                $hospital->getState(),
                $hospital->getDispatchArea(),
                $hospital->getSupplyArea(),
                $hospital->getLocation(),
                $hospital->getBeds(),
                $hospital->getSize()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->renderForm('hospital/edit.html.twig', [
                    'hospital' => $hospital,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'Hospital was successfully edited.');

            return $this->redirectToRoute('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('hospitals/edit.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
        ]);
    }

    #[Route(path: '/{id}', name: 'app_hospital_show', methods: ['GET'])]
    public function show(Hospital $hospital, AllocationRepository $allocationRepository, ImportRepository $importRepository): Response
    {
        $userIsOwner = $hospital->getOwner() == $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $userIsOwner = true;
        }

        return $this->render('hospitals/show.html.twig', [
            'hospital' => $hospital,
            'hospital_allocations' => $allocationRepository->countAllocations(),
            'hospital_imports' => $importRepository->countImports($hospital),
            'user_can_edit' => true,
        ]);
    }
}
