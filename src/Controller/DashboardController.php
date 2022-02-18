<?php

namespace App\Controller;

use App\Domain\Contracts\UserInterface;
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
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (null === $this->getUser()) {
            return $this->redirectToRoute('app_default');
        }

        if ($user->getHospitals()->isEmpty()) {
            $allocationCount = 0;
        } else {
            $allocationCount = $allocationRepository->countAllocationsByUser($user);
        }

        return $this->render('dashboard/index.html.twig', [
            'allocations' => $allocationRepository->countAllocations(),
            'hospitals' => $hospitalRepository->countHospitals(),
            'hospital_allocations' => $allocationCount,
            'users' => $userRepository->countUsers(),
            'user_imports' => $importRepository->countImports($user),
            'imports' => $importRepository->countImports(),
        ]);
    }
}
