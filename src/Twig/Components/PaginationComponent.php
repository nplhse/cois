<?php

namespace App\Twig\Components;

use App\DataTransferObjects\PaginationDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('pagination')]
class PaginationComponent
{
    public PaginationDto $dto;

    public int $nearbyPagesLimit = 2;

    #[ExposeInTemplate(name: 'total', getter: 'getTotal')]
    private int $total;

    #[ExposeInTemplate(name: 'previous', getter: 'getPrevious')]
    private ?int $previous;

    #[ExposeInTemplate(name: 'first', getter: 'getFirst')]
    private ?int $first;

    #[ExposeInTemplate(name: 'nearbyPagesLeft', getter: 'getNearbyLeft')]
    private array $nearbyLeft;

    #[ExposeInTemplate(name: 'current', getter: 'getCurrent')]
    private int $current;

    #[ExposeInTemplate(name: 'nearbyPagesRight', getter: 'getNearbyRight')]
    private array $nearbyRight;

    #[ExposeInTemplate(name: 'last', getter: 'getLast')]
    private ?int $last;

    #[ExposeInTemplate(name: 'next', getter: 'getNext')]
    private ?int $next;

    public function getTotal(): ?int
    {
        return $this->dto->getLast();
    }

    public function getPrevious(): ?int
    {
        return $this->dto->getPrevious();
    }

    public function getFirst(): ?int
    {
        $page = $this->dto->getPage();

        if ($page - $this->nearbyPagesLimit > 1) {
            return 1;
        }

        return null;
    }

    public function getNearbyLeft(): array
    {
        $page = $this->dto->getPage();

        return match ($page - $this->nearbyPagesLimit) {
            -2 => [],
            -1 => [$page - 1],
            default => [$page - 2, $page - 1],
        };
    }

    public function getCurrent(): int
    {
        return $this->dto->getPage();
    }

    public function getNearbyRight(): array
    {
        $last = $this->dto->getPage();

        return match ($this->dto->getPage() - $this->dto->getLast()) {
            0 => [],
            -1 => [$last + 1],
            default => [$last + 1, $last + 2],
        };
    }

    public function getLast(): ?int
    {
        $last = $this->dto->getLast();
        $page = $this->dto->getPage();
        $limit = $this->nearbyPagesLimit + 1;

        if ($page < $last - $limit) {
            return $this->dto->getLast();
        }

        return null;
    }

    public function getNext(): ?int
    {
        return $this->dto->getNext();
    }
}
