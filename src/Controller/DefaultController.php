<?php

namespace App\Controller;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository): Response
    {
        $user = $this->getUser();
        $hospital = $hospitalRepository->findOneByUser($user);

        return $this->render('default/index.html.twig', [
            'allocations' => $allocationRepository->countAllocations(),
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'hospitals' => $hospitalRepository->countHospitals(),
        ]);
    }
}
