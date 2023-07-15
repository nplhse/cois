<?php

declare(strict_types=1);

namespace Domain\Command\Comment;

use App\Entity\Comment;

class ApproveComment
{
    public function __construct(
        private readonly Comment $comment
    ) {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
