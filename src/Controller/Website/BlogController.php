<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/blog/', name: 'app_blog')]
    public function __invoke(Request $request): Response
    {
        $paginator = $this->postRepository->getPaginator($this->getPage($request));

        return $this->render('website/blog/index.html.twig', [
            'paginator' => $paginator,
            'sticky_posts' => $this->postRepository->findStickyPosts(),
        ]);
    }

    private function getPage(Request $request): int
    {
        $page = $request->query->get('page');

        if (is_numeric($page) && $page > 0) {
            return (int) $page;
        }

        return 1;
    }
}
