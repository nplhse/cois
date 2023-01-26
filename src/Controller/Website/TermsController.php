<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Domain\Enum\PageStatus;
use App\Domain\Enum\PageType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TermsController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    #[Route(path: [
        'en' => '/terms',
        'de' => '/nutzungsbedingungen',
    ], name: 'app_page_terms')]
    public function index(): Response
    {
        $page = $this->pageRepository->findOneBy(['type' => PageType::TERMS]);

        if (null === $page) {
            throw $this->createNotFoundException('Page could not be found');
        }

        if (!$this->isGranted('view', $page)) {
            if (PageStatus::DRAFT === $page->getStatus()) {
                throw $this->createNotFoundException('Page could not be found');
            }

            throw $this->createAccessDeniedException('You can not access this page.');
        }

        return $this->render('website/page/terms.html.twig', [
            'page' => $page,
        ]);
    }
}
