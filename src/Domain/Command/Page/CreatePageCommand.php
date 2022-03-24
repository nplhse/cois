<?php

namespace App\Domain\Command\Page;

use App\Domain\Contracts\UserInterface;

class CreatePageCommand
{
    private UserInterface $user;

    private string $title;

    private string $type;

    private string $status;

    private string $content;

    public function __construct(UserInterface $user, string $title, string $type, string $status, string $content)
    {
        $this->user = $user;
        $this->title = $title;
        $this->type = $type;
        $this->status = $status;
        $this->content = $content;
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
