<?php

declare(strict_types=1);

namespace Domain\Contracts;

interface DomainEventInterface
{
    public function getName(): string;
}
