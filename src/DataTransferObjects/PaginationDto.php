<?php

namespace App\DataTransferObjects;

class PaginationDto
{
    private int $page;

    private int $perPage;

    private ?int $previous;

    private ?int $next;

    private ?int $last;

    public function __construct(int $page, int $perPage, ?int $previous, ?int $next, int $last)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->previous = $previous;
        $this->next = $next;
        $this->last = $last;
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
