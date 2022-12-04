<?php

declare(strict_types=1);

namespace App\Domain\Command\Page;

class DeletePageCommand
{
    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
