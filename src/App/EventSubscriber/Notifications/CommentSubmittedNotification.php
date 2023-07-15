<?php

declare(strict_types=1);

namespace App\EventSubscriber\Notifications;

use Domain\Enum\CommentStatus;
use Domain\Event\Comment\CommentSubmittedEvent;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class CommentSubmittedNotification extends AbstractAdminNotification
{
    public function sendCommentSubmittedNotification(CommentSubmittedEvent $event): void
    {
        $comment = $event->getComment();

        if (CommentStatus::SUBMITTED !== $comment->getStatus()) {
            return;
        }

        $context = $this->getContext($event);

        foreach ($this->getRecipients() as $recipient) {
            $email = $this->getEmail(
                $recipient,
                'notification.comment.submitted.title',
                'emails/notifications/comment_submitted.inky.twig',
                $context,
            );

            $email->importance(NotificationEmail::IMPORTANCE_MEDIUM);

            $this->send($email);
        }
    }

    public function getContext(object $event): array
    {
        $comment = $event->getComment();

        return [
            'comment_id' => $comment->getId(),
            'comment_username' => $comment->getUsername(),
            'comment_email' => $comment->getEmail(),
            'comment_text' => $comment->getText(),
            'comment_approve_url' => $this->getRoute('admin_comment_approve', ['id' => $comment->getId()]),
            'comment_reject_url' => $this->getRoute('admin_comment_reject', ['id' => $comment->getId()]),
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CommentSubmittedEvent::NAME => ['sendCommentSubmittedNotification', 0],
        ];
    }
}
