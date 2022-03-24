<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private PageRepository $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    #[Route('/page/{slug}', name: 'app_page')]
    public function index(string $slug): Response
    {
        $slug = urldecode($slug);

        $page = $this->pageRepository->findOneBy(['slug' => urldecode($slug)]);

        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page %s could not be found', $slug));
        }

        return $this->render('page/index.html.twig', [
            'page' => $page,
        ]);
    }
}
