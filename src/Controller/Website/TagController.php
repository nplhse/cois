<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Tag;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/blog/tag/{slug}', name: 'app_blog_tag')]
    public function index(Tag $tag, Request $request): Response
    {
        $paginator = $this->postRepository->getTagPaginator($this->getPage($request), $tag);

        return $this->render('website/blog/tag.html.twig', [
            'tag' => $tag,
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
