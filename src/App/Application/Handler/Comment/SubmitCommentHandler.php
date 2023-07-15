<?php

declare(strict_types=1);

namespace App\Application\Handler\Comment;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Comment\SubmitComment;
use App\Domain\Enum\CommentStatus;
use App\Domain\Event\Comment\CommentSubmittedEvent;
use App\Entity\Comment;
use App\Repository\CommentRepository;

class SubmitCommentHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private CommentRepository $commentRepository
    ) {
    }

    public function __invoke(SubmitComment $command): void
    {
        $comment = new Comment();
        $comment->setText($command->getText());
        $comment->setPost($command->getPost());
        $comment->setCreatedAt(new \DateTimeImmutable());

        if ($command->getUser()) {
            /* @phpstan-ignore-next-line */
            $comment->setUser($command->getUser());
            $comment->setStatus(CommentStatus::APPROVED);
        }

        if ($command->getUsername() && $command->getEmail()) {
            $comment->setUsername($command->getUsername());
            $comment->setEmail($command->getEmail());
            $comment->setStatus($this->determineStatus($command));
        }

        $this->commentRepository->save($comment, true);

        $this->dispatchEvent(new CommentSubmittedEvent($comment));
    }

    private function determineStatus(SubmitComment $command): CommentStatus
    {
        if ($this->commentRepository->findApprovedPostsByUser($command->getEmail(), $command->getUsername()) > 0) {
            return CommentStatus::APPROVED;
        }

        if ($this->commentRepository->findRejectedPostsByUser($command->getEmail(), $command->getUsername()) > 0) {
            return CommentStatus::REJECTED;
        }

        return CommentStatus::SUBMITTED;
    }
}
