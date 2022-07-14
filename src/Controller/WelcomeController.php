<?php

namespace App\Controller;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    public function __construct(
        private AllocationRepository $allocationRepository,
        private HospitalRepository $hospitalRepository,
        private ImportRepository $importRepository,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/', name: 'app_welcome')]
    public function index(): Response
    {
        return $this->render('welcome/welcome.html.twig', [
            'allocationCount' => number_format((int) $this->allocationRepository->countAllocations(), 0, ',', '.'),
            'hospitalCount' => number_format((int) $this->hospitalRepository->countHospitals(), 0, ',', '.'),
            'importCount' => number_format((int) $this->importRepository->countImports(), 0, ',', '.'),
            'userCount' => number_format((int) $this->userRepository->countUsers(), 0, ',', '.'),
        ]);
    }
}
