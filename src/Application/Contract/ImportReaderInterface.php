<?php

namespace App\Application\Contract;

interface ImportReaderInterface
{
    public static function getFileType(): string;

    public function importData(string $path): iterable;
}
