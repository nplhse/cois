<?php

namespace App\Controller\Settings;

use App\Entity\Allocation;
use App\Form\AllocationType;
use App\Repository\AllocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/allocation')]
class AllocationController extends AbstractController
{
    #[Route('/', name: 'app_settings_allocation_index', methods: ['GET'])]
    public function index(Request $request, AllocationRepository $allocationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $filter = [];
        $filter['search'] = $request->query->get('search');

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $allocationRepository->getAllocationPaginator($offset, $filter);

        return $this->render('settings/allocation/index.html.twig', [
            'allocations' => $paginator,
            'search' => $filter['search'],
            'filters' => $filter,
            'perPage' => AllocationRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - AllocationRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AllocationRepository::PAGINATOR_PER_PAGE),
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
