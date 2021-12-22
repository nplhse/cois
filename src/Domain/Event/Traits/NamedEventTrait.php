<?php

declare(strict_types=1);

namespace App\Domain\Event\Traits;

trait NamedEventTrait
{
    public function getName(): string
    {
        return self::NAME;
    }
}
