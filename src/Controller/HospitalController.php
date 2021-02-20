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
 * @Route("/hospitals")
 * @IsGranted("ROLE_USER")
 */
class HospitalController extends AbstractController
{
    /**
     * @Route("/", name="hospital_index", methods={"GET"})
     */
    public function index(HospitalRepository $hospitalRepository): Response
    {
        return $this->render('hospital/index.html.twig', [
            'hospitals' => $hospitalRepository->findAll(),
            'user_hospital' => $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]),
        ]);
    }

    /**
     * @Route("/edit", name="hospital_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $user = $this->getUser();
        $hospital = $hospitalRepository->findOneBy(['owner' => $user->getId()]);

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setUpdatedAt(new \DateTime('NOW'));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hospital_index');
        }

        return $this->render('hospital/edit.html.twig', [
            'hospital' => $hospital,
            'form' => $form->createView(),
            'user_hospital' => $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]),
        ]);
    }

    /**
     * @Route("/{id}", name="hospital_show", methods={"GET"})
     */
    public function show(Hospital $hospital, AllocationRepository $allocationRepository): Response
    {
        if ($hospital->getOwner() == $this->getUser()) {
            $userIsOwner = true;
        } else {
            $userIsOwner = false;
        }

        return $this->render('hospital/show.html.twig', [
            'hospital' => $hospital,
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'user_is_owner' => $userIsOwner,
        ]);
    }
}
