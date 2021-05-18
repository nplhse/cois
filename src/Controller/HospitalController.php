<?php

namespace App\Controller;

use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/hospitals")
 * @IsGranted("ROLE_USER")
 */
class HospitalController extends AbstractController
{
    /**
     * @Route("/", name="hospital_index", methods={"GET"})
     */
    public function index(HospitalRepository $hospitalRepository): Response
    {
        $userHospital = $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]);
        $userIsOwner = $userHospital;

        return $this->render('hospital/index.html.twig', [
            'hospitals' => $hospitalRepository->findAll(),
            'user_hospital' => $userHospital,
            'user_is_owner' => $userIsOwner,
        ]);
    }

    /**
     * @Route("/edit", name="hospital_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $hospital = $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]);

        if (!$hospital) {
            $this->addFlash('danger', 'You been redirected, because you have to be owner of a hospital in order to access this page.');

            return $this->redirectToRoute('hospital_index');
        }

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setUpdatedAt(new \DateTime('NOW'));

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Hospital was successfully edited.');

            return $this->redirectToRoute('hospital_index');
        }

        return $this->render('hospital/edit.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
            'user_hospital' => $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]),
        ]);
    }

    /**
     * @Route("/{id}", name="hospital_show", methods={"GET"})
     */
    public function show(Hospital $hospital, AllocationRepository $allocationRepository): Response
    {
        $userIsOwner = $hospital->getOwner() == $this->getUser();

        return $this->render('hospital/show.html.twig', [
            'hospital' => $hospital,
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'user_is_owner' => $userIsOwner,
        ]);
    }
}
