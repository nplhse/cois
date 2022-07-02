<?php

namespace App\Twig\Components;

use App\DataTransferObjects\PaginationDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('pagination')]
class PaginationComponent
{
    public PaginationDto $dto;

    public int $nearbyPagesLimit;

    public int $total;

    public ?int $previous;

    public ?int $first;

    public array $nearbyPagesLeft = [];

    public int $current;

    public array $nearbyPagesRight = [];

    public ?int $last;

    public ?int $next;

    public function mount(PaginationDto $dto, int $nearbyPagesLimit = 2): void
    {
        $this->dto = $dto;
        $this->nearbyPagesLimit = $nearbyPagesLimit;

        $this->total = $this->dto->getLast();
        $this->current = $this->dto->getPage();
        $this->previous = $this->dto->getPrevious();
        $this->next = $this->dto->getNext();

        $this->first = $this->getFirst();
        $this->last = $this->getLast();
        $this->nearbyPagesLeft = $this->getNearbyPagesLeft();
        $this->nearbyPagesRight = $this->getNearbyPagesRight();
    }

    private function getFirst(): ?int
    {
        if (($this->current - $this->nearbyPagesLimit) > 1) {
            return 1;
        }

        return null;
    }

    private function getNearbyPagesLeft(): array
    {
        if (1 === $this->current) {
            return $this->nearbyPagesLeft;
        }

        $t = $this->current;

        for ($i = 1; $i <= $this->nearbyPagesLimit; ++$i) {
            --$t;

            if ($t <= 0) {
                break;
            }

            $this->nearbyPagesLeft[] = $t;
        }

        return array_reverse($this->nearbyPagesLeft);
    }

    private function getNearbyPagesRight(): array
    {
        if ($this->current === $this->total) {
            return $this->nearbyPagesRight;
        }

        $t = $this->current;

        for ($i = 1; $i <= $this->nearbyPagesLimit; ++$i) {
            ++$t;

            if ($t > $this->total) {
                break;
            }

            $this->nearbyPagesRight[] = $t;
        }

        return $this->nearbyPagesRight;
    }

    private function getLast(): ?int
    {
        if ($this->current < ($this->total - $this->nearbyPagesLimit)) {
            return $this->total;
        }

        return null;
    }
}
