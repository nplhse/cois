<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/blog/', name: 'app_blog')]
    public function __invoke(): Response
    {
        return $this->render('website/blog/index.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);
    }
}
