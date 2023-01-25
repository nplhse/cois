<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Domain\Enum\PageType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImprintController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    #[Route(path: [
        'en' => '/imprint',
        'de' => '/impressum',
    ], name: 'app_page_imprint')]
    public function index(): Response
    {
        $page = $this->pageRepository->findOneBy(['type' => PageType::IMPRINT]);

        if (null === $page) {
            throw $this->createNotFoundException('Page could not be found');
        }

        return $this->render('website/page/imprint.html.twig', [
            'page' => $page,
        ]);
    }
}
