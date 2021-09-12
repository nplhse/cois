<?php

namespace App\Controller\Settings;

use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/hospital')]
class HospitalController extends AbstractController
{
    #[Route('/', name: 'app_settings_hospital_index', methods: ['GET'])]
    public function index(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $filter = [];
        $filter['search'] = $request->query->get('search');
        $filter['location'] = null;
        $filter['size'] = null;
        $filter['supplyArea'] = null;
        $filter['dispatchArea'] = null;

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $hospitalRepository->getHospitalPaginator($offset, $filter);

        return $this->render('settings/hospital/index.html.twig', [
            'hospitals' => $paginator,
            'search' => $filter['search'],
            'filters' => $filter,
            'perPage' => HospitalRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - HospitalRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + HospitalRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_hospital_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $hospital = new Hospital();
        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hospital);
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_hospital_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/hospital/new.html.twig', [
            'hospital' => $hospital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_hospital_show', methods: ['GET'])]
    public function show(Hospital $hospital): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('settings/hospital/show.html.twig', [
            'hospital' => $hospital,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_hospital_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hospital $hospital): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_settings_hospital_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/hospital/edit.html.twig', [
            'hospital' => $hospital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_hospital_delete', methods: ['POST'])]
    public function delete(Request $request, Hospital $hospital): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$hospital->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hospital);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_hospital_index', [], Response::HTTP_SEE_OTHER);
    }
}
