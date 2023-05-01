<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Pagination\Paginator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('paginate')]
final class PaginateComponent
{
    public Paginator $paginator;

    public int $nearbyPagesLimit;

    private array $nearbyPagesLeft = [];

    private array $nearbyPagesRight = [];

    public function mount(Paginator $paginator, int $nearByPageslimit = 3): void
    {
        $this->paginator = $paginator;
        $this->nearbyPagesLimit = $nearByPageslimit;
    }

    public function getCurrentPage(): int
    {
        return $this->paginator->getCurrentPage();
    }

    public function getLastPage(): int
    {
        return $this->paginator->getLastPage();
    }

    public function getPageSize(): int
    {
        return $this->paginator->getPageSize();
    }

    public function hasPreviousPage(): bool
    {
        return $this->paginator->hasPreviousPage();
    }

    public function getPreviousPage(): int
    {
        return $this->paginator->getPreviousPage();
    }

    public function hasNextPage(): bool
    {
        return $this->paginator->hasNextPage();
    }

    public function getNextPage(): int
    {
        return $this->paginator->getNextPage();
    }

    public function getFirstPage(): ?int
    {
        if (($this->paginator->getCurrentPage() - $this->nearbyPagesLimit) > 1) {
            return 1;
        }

        return null;
    }

    public function getNearbyPagesLeft(): array
    {
        if (1 === $this->paginator->getCurrentPage()) {
            return $this->nearbyPagesLeft;
        }

        $t = $this->paginator->getCurrentPage();

        for ($i = 1; $i <= $this->nearbyPagesLimit; ++$i) {
            --$t;

            if ($t <= 0) {
                break;
            }

            $this->nearbyPagesLeft[] = $t;
        }

        return array_reverse($this->nearbyPagesLeft);
    }

    public function getNearbyPagesRight(): array
    {
        if ($this->paginator->getCurrentPage() === $this->paginator->getLastPage()) {
            return $this->nearbyPagesRight;
        }

        $t = $this->paginator->getCurrentPage();

        for ($i = 1; $i <= $this->nearbyPagesLimit; ++$i) {
            ++$t;

            if ($t > $this->paginator->getLastPage()) {
                break;
            }

            $this->nearbyPagesRight[] = $t;
        }

        return $this->nearbyPagesRight;
    }

    public function hasLastPage(): bool
    {
        if ($this->paginator->getCurrentPage() < ($this->paginator->getLastPage() - $this->nearbyPagesLimit)) {
            return true;
        }

        return false;
    }

    public function hasToPaginate(): bool
    {
        return $this->paginator->hasToPaginate();
    }

    public function getNumResults(): int
    {
        return $this->paginator->getNumResults();
    }
}
