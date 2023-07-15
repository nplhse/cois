<?php

declare(strict_types=1);

namespace Domain\Event\Comment;

use App\Entity\Comment;
use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class CommentApprovedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'comment.approved';

    public function __construct(
        private Comment $comment
    ) {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
