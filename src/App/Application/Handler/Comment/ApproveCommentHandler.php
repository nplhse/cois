<?php

declare(strict_types=1);

namespace App\Application\Handler\Comment;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Comment\ApproveComment;
use App\Domain\Enum\CommentStatus;
use App\Domain\Event\Comment\CommentApprovedEvent;
use App\Repository\CommentRepository;

class ApproveCommentHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private CommentRepository $commentRepository
    ) {
    }

    public function __invoke(ApproveComment $command): void
    {
        $comment = $command->getComment();
        $comment->setStatus(CommentStatus::APPROVED);

        $this->commentRepository->save($comment, true);

        $this->dispatchEvent(new CommentApprovedEvent($comment));
    }
}
