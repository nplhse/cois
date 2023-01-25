<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    public function __construct(
        private PostRepository $postRepository,
        private AllocationRepository $allocationRepository,
        private HospitalRepository $hospitalRepository,
        private ImportRepository $importRepository,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/', name: 'app_homepage')]
    public function index(Request $request): Response
    {
        return $this->render('website/homepage/homepage.html.twig', [
            'posts' => $this->postRepository->findRecentPosts(4),
            'allocationCount' => $this->allocationRepository->countAllocations(),
            'hospitalCount' => $this->hospitalRepository->countHospitals(),
            'importCount' => $this->importRepository->countImports(),
            'userCount' => $this->userRepository->countUsers(),
        ]);
    }
}
