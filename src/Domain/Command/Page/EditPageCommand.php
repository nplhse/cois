<?php

declare(strict_types=1);

namespace Domain\Command\Page;

use Domain\Contracts\UserInterface;

class EditPageCommand
{
    public function __construct(
        private int $id,
        private UserInterface $user,
        private string $title,
        private string $type,
        private string $status,
        private string $content
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
