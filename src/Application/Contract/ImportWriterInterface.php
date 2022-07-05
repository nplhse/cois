<?php

namespace App\Application\Contract;

use App\Domain\Contracts\ImportInterface;

interface ImportWriterInterface
{
    public static function getDataType(): string;

    public static function getPriority(): int;

    public function processData(array $row, ImportInterface $import): ?object;
}
