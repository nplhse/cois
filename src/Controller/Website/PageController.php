<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private array $protectedSlugs = [
        'imprint',
        'terms',
        'privacy',
    ];

    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    #[Route('/page/{slug}', name: 'app_page')]
    public function index(string $slug): Response
    {
        if (in_array($slug, $this->protectedSlugs)) {
            $redirect = match ($slug) {
                'imprint' => 'app_page_imprint',
                'terms' => 'app_page_terms',
                'privacy' => 'app_page_privacy',
            };

            return $this->redirectToRoute($redirect);
        }

        $page = $this->pageRepository->findOneBy(['slug' => $slug]);

        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page %s could not be found', $slug));
        }

        $this->denyAccessUnlessGranted('view', $page);

        return $this->render('website/page/index.html.twig', [
            'page' => $page,
        ]);
    }
}
