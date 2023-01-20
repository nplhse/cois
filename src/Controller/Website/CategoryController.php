<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Category;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/blog/category/{slug}', name: 'app_blog_category')]
    public function index(Category $category, Request $request): Response
    {
        $paginator = $this->postRepository->getCategoryPaginator($this->getPage($request), $category);

        return $this->render('website/blog/category.html.twig', [
            'category' => $category,
            'paginator' => $paginator,
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
