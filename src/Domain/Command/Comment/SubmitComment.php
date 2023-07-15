<?php

declare(strict_types=1);

namespace Domain\Command\Comment;

use App\Entity\Post;
use Domain\Contracts\UserInterface;

class SubmitComment
{
    public function __construct(
        private readonly string $text,
        private readonly Post $post,
        private readonly ?string $username,
        private readonly ?string $email,
        private readonly ?UserInterface $user
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }
}
