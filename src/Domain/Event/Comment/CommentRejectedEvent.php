<?php

declare(strict_types=1);

namespace Domain\Event\Comment;

use App\Entity\Comment;
use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class CommentRejectedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'comment.rejected';

    public function __construct(
        private Comment $comment
    ) {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
