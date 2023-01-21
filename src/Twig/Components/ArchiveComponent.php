<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Query\BlogArchiveQuery;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('archive')]
final class ArchiveComponent
{
    public function __construct(
        private readonly BlogArchiveQuery $query,
    ) {
    }

    public function getItems(): array
    {
        $items = $this->query->execute()->getItems();
        $parsed = [];

        foreach ($items as $item) {
            $row = [];
            $row['label'] = date('F Y', mktime(0, 0, 0, $item['month'], 1, $item['year']));
            $row['count'] = $item['count'];
            $row['month'] = $item['month'];
            $row['year'] = $item['year'];

            $parsed[] = $row;
        }

        return $parsed;
    }
}
