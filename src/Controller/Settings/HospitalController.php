<?php

namespace App\Controller\Settings;

use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
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

        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['hospital'] = $paramService->getHospital();
        $filters['supplyArea'] = $paramService->getSupplyArea();
        $filters['dispatchArea'] = $paramService->getDispatchArea();
        $filters['location'] = $paramService->getLocation();
        $filters['size'] = $paramService->getSize();

        $paginator = $hospitalRepository->getHospitalPaginator($paramService->getPage(), $filters);

        return $this->render('settings/hospital/index.html.twig', [
            'hospitals' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), HospitalRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_hospital_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $hospital = new Hospital();
        $form = $this->createForm(HospitalType::class, $hospital, ['backend' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setCreatedAt(new \DateTime('NOW'));
            $hospital->setUpdatedAt(new \DateTime('NOW'));

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

        $form = $this->createForm(HospitalType::class, $hospital, ['backend' => true]);
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

        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$hospital->getId(), $CsrfToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hospital);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_hospital_index', [], Response::HTTP_SEE_OTHER);
    }
}
