<?php

namespace App\Controller;

use App\Domain\Contracts\UserInterface;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private AllocationRepository $allocationRepository,
        private HospitalRepository $hospitalRepository,
        private ImportRepository $importRepository,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/dashboard/', name: 'app_dashboard')]
    public function index(): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'allocationCount' => $this->allocationRepository->countAllocations(),
            'hospitalCount' => $this->hospitalRepository->countHospitals(),
            'importCount' => $this->importRepository->countImports(),
            'userCount' => $this->userRepository->countUsers(),
            'userAllocationCount' => $this->allocationRepository->countAllocationsByUser($user),
            'userHospitalCount' => $this->hospitalRepository->countHospitalsByUsers($user),
            'userImportCount' => $this->importRepository->countImportsByUser($user),
        ]);
    }
}
