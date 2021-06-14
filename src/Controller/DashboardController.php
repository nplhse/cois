<?php

namespace App\Controller;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/dashboard/", name="dashboard")
     */
    public function index(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository, UserRepository $userRepository, ImportRepository $importRepository): Response
    {
        $user = $this->getUser();
        $hospital = $hospitalRepository->findOneByUser($user);

        return $this->render('dashboard/index.html.twig', [
            'allocations' => $allocationRepository->countAllocations(),
            'hospitals' => $hospitalRepository->countHospitals(),
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'users' => $userRepository->countUsers(),
            'user_imports' => $importRepository->countImports($user),
            'imports' => $importRepository->countImports(),
        ]);
    }
}
