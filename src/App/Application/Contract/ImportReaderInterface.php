<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface ImportReaderInterface
{
    public static function getFileType(): string;

    public function importData(string $path): iterable;
}
