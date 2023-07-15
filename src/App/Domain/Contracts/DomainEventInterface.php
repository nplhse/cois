<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface DomainEventInterface
{
    public function getName(): string;
}
