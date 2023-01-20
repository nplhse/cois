<?php

declare(strict_types=1);

namespace App\Application\Handler\Comment;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Comment\RejectComment;
use App\Domain\Enum\CommentStatus;
use App\Domain\Event\Comment\CommentRejectedEvent;
use App\Repository\CommentRepository;

class RejectCommentHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private CommentRepository $commentRepository
    ) {
    }

    public function __invoke(RejectComment $command): void
    {
        $comment = $command->getComment();
        $comment->setStatus(CommentStatus::REJECTED);

        $this->commentRepository->save($comment, true);

        $this->dispatchEvent(new CommentRejectedEvent($comment));
    }
}
