<?php

declare(strict_types=1);

namespace Domain\Event;

trait NamedEventTrait
{
    public function getName(): string
    {
        return self::NAME;
    }
}
