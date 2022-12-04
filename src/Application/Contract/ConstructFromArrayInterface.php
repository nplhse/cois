<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface ConstructFromArrayInterface
{
    public static function fromArray(array $array): object;
}
