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

class CommentController extends AbstractController
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {
    }

    #[Route('/blog/{id}/comments', name: 'app_post_comments')]
    public function invoke(Post $post, Request $request): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_post_comments', [
                'id' => $post->getId(),
            ]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                /* @phpstan-ignore-next-line */
                $comment->setUser($this->getUser());
            }

            $comment = $this->setStatus($comment);

            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $this->commentRepository->save($comment, true);

            if (CommentStatus::APPROVED === $comment->getStatus()) {
                return $this->redirectToRoute('app_post_comments', [
                    'id' => $post->getId(),
                ]);
            }

            $view = 'website/blog/_comments_approval.html.twig';
        }

        if (empty($view)) {
            $view = 'website/blog/_comments.html.twig';
        }

        $comments = $this->commentRepository->findCommentsByPost($post->getId());

        return $this->render($view, [
            'comments' => $comments,
            'post' => $post,
            'form' => $form,
        ]);
    }

    private function setStatus(Comment $comment): Comment
    {
        if ($comment->getUser()) {
            return $comment->setStatus(CommentStatus::APPROVED);
        }

        return $comment->setStatus(CommentStatus::SUBMITTED);
    }
}
