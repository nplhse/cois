<?php

declare(strict_types=1);

namespace App\Application\Contract;

use Domain\Contracts\ImportInterface;

interface AllocationImportWriterInterface
{
    public function process(?object $entity, array $row, ImportInterface $import): ?object;
}
