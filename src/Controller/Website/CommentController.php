<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Domain\Command\Comment\SubmitComment;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly CommentRepository $commentRepository
    ) {
    }

    /**
     * @psalm-suppress InvalidArgument
     */
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
                $user = $this->getUser();
            } else {
                $user = null;
            }

            $command = new SubmitComment(
                $comment->getText(),
                $post,
                $comment->getUsername(),
                $comment->getEmail(),
                /* @phpstan-ignore-next-line */
                $user
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException $exception) {
                $this->addFlash('danger', 'Sorry, something went wrong.'.$exception->getMessage());
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
}
