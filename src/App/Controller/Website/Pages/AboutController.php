<?php

declare(strict_types=1);

namespace App\Controller\Website\Pages;

use App\Repository\PageRepository;
use App\Repository\UserRepository;
use Domain\Enum\PageStatus;
use Domain\Enum\PageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository,
        private UserRepository $userRepository
    ) {
    }

    #[Route(path: [
        'en' => '/about',
        'de' => '/ueber-uns',
    ], name: 'app_page_about')]
    public function index(): Response
    {
        $page = $this->pageRepository->findOneBy(['type' => PageType::ABOUT]);

        if (null === $page) {
            throw $this->createNotFoundException('Page could not be found');
        }

        if (!$this->isGranted('view', $page)) {
            if (PageStatus::DRAFT === $page->getStatus()) {
                throw $this->createNotFoundException('Page could not be found');
            }

            throw $this->createAccessDeniedException('You can not access this page.');
        }

        return $this->render('website/page/about.html.twig', [
            'page' => $page,
            'users' => $this->userRepository->findImportantUsers(),
        ]);
    }
}
