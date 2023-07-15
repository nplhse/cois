<?php

declare(strict_types=1);

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\CommentCrudController;
use App\Entity\Comment;
use Domain\Command\Comment\RejectComment;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/comment/{id}/approve/', name: 'admin_comment_reject')]
class CommentRejectController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(Comment $comment): Response
    {
        $command = new RejectComment(
            $comment,
        );

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $this->addFlash('danger', 'Sorry, something went wrong.'.$exception->getMessage());
        }

        $this->addFlash('success', 'Comment has been approved.');

        return $this->redirect($this->adminUrlGenerator->setController(CommentCrudController::class)
            ->setAction('detail')
            ->setEntityId($comment->getId())
            ->generateUrl());
    }
}
