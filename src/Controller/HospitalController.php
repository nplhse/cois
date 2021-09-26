<?php

namespace App\Controller;

use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/hospitals')]
class HospitalController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_hospital_index')]
    public function index(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $search = $request->query->get('search');
        $location = $request->query->get('location');
        $size = $request->query->get('size');
        $supplyArea = $request->query->get('supplyArea');
        $dispatchArea = $request->query->get('dispatchArea');

        $filters = [];
        $filters['search'] = $search;

        if ($location) {
            $filters['location'] = $location;
        } else {
            $filters['location'] = null;
        }

        if ($size) {
            $filters['size'] = $size;
        } else {
            $filters['size'] = null;
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
        $paginator = $hospitalRepository->getHospitalPaginator($offset, $filters);

        return $this->render('hospitals/index.html.twig', [
            'hospitals' => $paginator,
            'search' => $search,
            'perPage' => HospitalRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - HospitalRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + HospitalRepository::PAGINATOR_PER_PAGE),
            'filters' => $filters,
            'supplyAreas' => $hospitalRepository->getSupplyAreas(),
            'dispatchAreas' => $hospitalRepository->getDispatchAreas(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_hospital_edit", methods={"GET","POST"})
     */
    public function edit(Hospital $hospital, Request $request, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $hospital);

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setUpdatedAt(new \DateTime('NOW'));

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Hospital was successfully edited.');

            return $this->redirectToRoute('app_hospital_index');
        }

        return $this->render('hospitals/edit.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
        ]);
    }

    /**
     * @Route("/{id}", name="app_hospital_show", methods={"GET"})
     */
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
}
