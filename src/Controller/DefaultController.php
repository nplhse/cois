<?php

namespace App\Controller;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Repository\UserRepository;
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
     * @Route("/", name="app_no_locale")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('default', ['_locale' => 'en']);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="default")
     */
    public function index(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository, UserRepository $userRepository, ImportRepository $importRepository): Response
    {
        $user = $this->getUser();
        $hospital = $hospitalRepository->findOneByUser($user);

        return $this->render('default/dashboard.html.twig', [
            'allocations' => $allocationRepository->countAllocations(),
            'hospitals' => $hospitalRepository->countHospitals(),
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'users' => $userRepository->countUsers(),
            'user_imports' => $importRepository->countImports($user),
            'imports' => $importRepository->countImports(),
        ]);
    }
}
