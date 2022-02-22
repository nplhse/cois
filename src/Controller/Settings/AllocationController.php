<?php

namespace App\Controller\Settings;

use App\Domain\Repository\DispatchAreaRepositoryInterface;
use App\Domain\Repository\SupplyAreaRepositoryInterface;
use App\Entity\Allocation;
use App\Form\AllocationType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/allocation')]
class AllocationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private SupplyAreaRepositoryInterface $supplyAreaRepository;

    private DispatchAreaRepositoryInterface $dispatchAreaRepository;

    public function __construct(EntityManagerInterface $entityManager, SupplyAreaRepositoryInterface $supplyAreaRepository, DispatchAreaRepositoryInterface $dispatchAreaRepository)
    {
        $this->entityManager = $entityManager;
        $this->supplyAreaRepository = $supplyAreaRepository;
        $this->dispatchAreaRepository = $dispatchAreaRepository;
    }

    #[Route('/', name: 'app_settings_allocation_index', methods: ['GET'])]
    public function index(Request $request, AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['hospital'] = $paramService->getHospital();
        $filters['supplyArea'] = $paramService->getSupplyArea();
        $filters['dispatchArea'] = $paramService->getDispatchArea();
        $filters['startDate'] = $paramService->getStartDate();
        $filters['endDate'] = $paramService->getEndDate();

        $paginator = $allocationRepository->getAllocationPaginator($paramService->getPage());

        return $this->render('settings/allocation/index.html.twig', [
            'allocations' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), AllocationRepository::PAGINATOR_PER_PAGE),
            'supplyAreas' => $this->supplyAreaRepository->findAll(),
            'dispatchAreas' => $this->dispatchAreaRepository->findAll(),
            'hospitals' => $hospitalRepository->findAll(),
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

    #[Route('/{id}/edit', name: 'app_settings_allocation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Allocation $allocation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(AllocationType::class, $allocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_settings_allocation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/allocation/edit.html.twig', [
            'allocation' => $allocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_allocation_delete', methods: ['POST'])]
    public function delete(Request $request, Allocation $allocation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$allocation->getId(), $CsrfToken)) {
            $this->entityManager->remove($allocation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_allocation_index', [], Response::HTTP_SEE_OTHER);
    }
}
