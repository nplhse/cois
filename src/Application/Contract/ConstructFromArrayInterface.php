<?php

namespace App\Application\Contract;

interface ConstructFromArrayInterface
{
    public static function fromArray(array $array): object;
}
