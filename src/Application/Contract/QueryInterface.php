<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Service\FilterService;

interface QueryInterface
{
    public function execute(?FilterService $filterService = null): ResultCollectionInterface;
}
