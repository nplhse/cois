<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/blog/rss.xml', defaults: ['page' => '1', '_format' => 'xml'], methods: ['GET'], name: 'app_blog_rss')]
    public function __invoke(Request $request): Response
    {
        $paginator = $this->postRepository->getFeedPaginator($this->getPage($request));

        $rss = [
            'title' => $this->getParameter('app.title'),
            'description' => 'The Blog of '.$this->getParameter('app.title'),
        ];

        return $this->render('website/blog/rss.xml', [
            'rss' => $rss,
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
