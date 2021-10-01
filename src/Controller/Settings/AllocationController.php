<?php

namespace App\Controller\Settings;

use App\Entity\Allocation;
use App\Form\AllocationType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/allocation')]
class AllocationController extends AbstractController
{
    #[Route('/', name: 'app_settings_allocation_index', methods: ['GET'])]
    public function index(Request $request, AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $hospital = $request->query->get('hospital');
        $supplyArea = $request->query->get('supplyArea');
        $dispatchArea = $request->query->get('dispatchArea');

        $filters = [];
        $filters['search'] = $request->query->get('search');

        if ($hospital) {
            $filters['hospital'] = $hospital;
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

        return $this->render('settings/allocation/index.html.twig', [
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
            $this->getDoctrine()->getManager()->flush();

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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($allocation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_allocation_index', [], Response::HTTP_SEE_OTHER);
    }
}
