<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Domain\Enum\CommentStatus;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {
    }

    #[Route('/blog/{slug}', name: 'app_post')]
    public function invoke(Post $post, Request $request): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_post', [
                'slug' => $post->getSlug(),
            ]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $comment->setUser($this->getUser());
            }

            $comment->setPost($post);
            $comment->setStatus(CommentStatus::APPROVED);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $this->commentRepository->save($comment, true);

            $this->redirectToRoute('app_blog');
        }

        return $this->render('website/blog/post.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
}
