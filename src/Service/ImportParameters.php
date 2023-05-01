<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class ImportParameters
{
    private int $page = 1;

    private string $sortBy = 'asc';

    private string $orderBy = 'createdAt';

    public static function createFromRequest(Request $request): ImportParameters
    {
        $page = (int) $request->query->get('page');
        $orderBy = (string) $request->query->get('orderBy');
        $sortBy = (string) $request->query->get('sortBy');

        return new ImportParameters($page, $orderBy, $sortBy);
    }

    public function __construct(int $page, string $orderBy, string $sortBy)
    {
        if ($page > 0) {
            $this->page = $page;
        }

        if (\in_array($orderBy, ['id', 'createdAt'], true)) {
            $this->orderBy = $orderBy;
        }

        if (\in_array($sortBy, ['asc', 'desc'], true)) {
            $this->sortBy = $sortBy;
        }
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }
}
