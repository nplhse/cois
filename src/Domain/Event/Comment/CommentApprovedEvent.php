<?php

declare(strict_types=1);

namespace App\Domain\Event\Comment;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use App\Entity\Comment;
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
