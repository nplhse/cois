<?php

namespace App\Controller;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository, UserRepository $userRepository, ImportRepository $importRepository): Response
    {
        return $this->render('default/welcome.html.twig', [
            'allocationCount' => $allocationRepository->countAllocations(),
            'hospitalCount' => $hospitalRepository->countHospitals(),
            'importCount' => $importRepository->countImports(),
        ]);
    }
}
