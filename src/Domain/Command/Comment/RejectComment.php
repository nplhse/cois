<?php

declare(strict_types=1);

namespace App\Domain\Command\Comment;

use App\Entity\Comment;

class RejectComment
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
