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
    #[Route('/dashboard/', name: 'app_dashboard')]
    public function index(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository, UserRepository $userRepository, ImportRepository $importRepository): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            return $this->redirectToRoute('app_default');
        }

        return $this->render('dashboard/index.html.twig', [
            'allocations' => $allocationRepository->countAllocations(),
            'hospitals' => $hospitalRepository->countHospitals(),
            'hospital_allocations' => $allocationRepository->countAllocations($hospitalRepository->findOneByUser($user)),
            'users' => $userRepository->countUsers(),
            'user_imports' => $importRepository->countImports($user),
            'imports' => $importRepository->countImports(),
        ]);
    }
}
