<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

class PaginationDto
{
    public function __construct(
        private int $page,
        private int $perPage,
        private ?int $previous,
        private ?int $next,
        private ?int $last
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPrevious(): ?int
    {
        return $this->previous;
    }

    public function getNext(): ?int
    {
        return $this->next;
    }

    public function getLast(): ?int
    {
        return $this->last;
    }
}
