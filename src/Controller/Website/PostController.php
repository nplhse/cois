<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/blog/{id}', name: 'app_post')]
    public function invoke(Post $post): Response
    {
        return $this->render('website/blog/post.html.twig', [
            'post' => $post,
        ]);
    }
}
