<?php

namespace App\Domain\Command\Page;

use App\Domain\Contracts\UserInterface;

class EditPageCommand
{
    private int $id;

    private UserInterface $user;

    private string $title;

    private string $type;

    private string $status;

    private string $content;

    public function __construct(int $id, UserInterface $user, string $title, string $type, string $status, string $content)
    {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->type = $type;
        $this->status = $status;
        $this->content = $content;
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
