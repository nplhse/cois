<?php

namespace App\Controller;

use App\Domain\Command\Hospital\CreateHospitalCommand;
use App\Domain\Contracts\HospitalInterface;
use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\AllocationRepository;
use App\Repository\DispatchAreaRepository;
use App\Repository\HospitalRepository;
use App\Repository\SupplyAreaRepository;
use App\Service\AdminNotificationService;
use App\Service\RequestParamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/hospitals')]
class HospitalController extends AbstractController
{
    private MessageBusInterface $messageBus;

    private AdminNotificationService $adminNotifier;

    private Security $security;

    private EntityManagerInterface $entityManager;

    public function __construct(MessageBusInterface $messageBus, AdminNotificationService $adminNotifier, Security $security, EntityManagerInterface $entityManager)
    {
        $this->messageBus = $messageBus;
        $this->adminNotifier = $adminNotifier;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_hospital_index')]
    public function index(Request $request, HospitalRepository $hospitalRepository, SupplyAreaRepository $supplyAreaRepository, DispatchAreaRepository $dispatchAreaRepository): Response
    {
        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['location'] = $paramService->getLocation();
        $filters['size'] = $paramService->getSize();
        $filters['supplyArea'] = $paramService->getSupplyArea();
        $filters['dispatchArea'] = $paramService->getDispatchArea();

        $filters['sortBy'] = $paramService->getSortBy();

        if (!$paramService->getSortBy()) {
            $filters['orderBy'] = 'asc';
        } else {
            $filters['orderBy'] = $paramService->getOrderBy();
        }

        $paginator = $hospitalRepository->getHospitalPaginator($paramService->getPage(), $filters);

        return $this->render('hospitals/index.html.twig', [
            'hospitals' => $paginator,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), HospitalRepository::PAGINATOR_PER_PAGE),
            'filters' => $filters,
            'filterIsSet' => $paramService->isFilterIsSet(),
            'locations' => $this->getLocations(),
            'sizes' => $this->getSizes(),
            'supplyAreas' => $supplyAreaRepository->findAll(),
            'dispatchAreas' => $dispatchAreaRepository->findAll(),
        ]);
    }

    #[Route(path: '/new', name: 'app_hospital_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $hospital = new Hospital();

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateHospitalCommand(
                $this->getUser(),
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

            $this->adminNotifier->sendNewHospitalNotification($hospital);

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

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setUpdatedAt(new \DateTime('NOW'));

            $this->entityManager->flush();

            $this->addFlash('success', 'Hospital was successfully edited.');

            return $this->redirectToRoute('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('hospitals/edit.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
        ]);
    }

    #[Route(path: '/{id}', name: 'app_hospital_show', methods: ['GET'])]
    public function show(Hospital $hospital, AllocationRepository $allocationRepository): Response
    {
        $userIsOwner = $hospital->getOwner() == $this->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $userIsOwner = true;
        }

        return $this->render('hospitals/show.html.twig', [
            'hospital' => $hospital,
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'user_can_edit' => true,
        ]);
    }

    private function getLocations(): array
    {
        return [
            [
                'element' => 'rural',
            ],
            [
                'element' => 'urban',
            ],
        ];
    }

    private function getSizes(): array
    {
        return [
            [
                'element' => 'small',
            ],
            [
                'element' => 'medium',
            ],
            [
                'element' => 'large',
            ],
        ];
    }
}
