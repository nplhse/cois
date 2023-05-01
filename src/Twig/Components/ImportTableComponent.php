<?php

namespace App\Twig\Components;

use App\Domain\Repository\ImportRepositoryInterface;
use App\Pagination\Paginator;
use App\Repository\ImportRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('import_table')]
final class ImportTableComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public string $sortBy = 'createdAt';

    #[LiveProp(writable: true)]
    public string $orderBy = 'desc';

    private \Traversable|null $imports = null;

    public function __construct(
        private ImportRepository $importRepository,
    ) {

    }

    public function getImports(): \Traversable
    {
        if (null === $this->imports) {
            $this->imports = $this->getPaginator()->getResults();
        }

        return $this->imports;
    }

    public function getPaginator(): Paginator
    {
        return $this->importRepository->getPaginator($this->page, $this->sortBy, $this->sortBy);
    }
}
