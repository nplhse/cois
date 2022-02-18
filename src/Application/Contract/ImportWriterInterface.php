<?php

namespace App\Application\Contract;

use App\Domain\Contracts\ImportInterface;

interface ImportWriterInterface
{
    public static function getDataType(): string;

    public static function getPriority(): int;

    public function processData(?object $entity, array $row, ImportInterface $import): ?object;
}
