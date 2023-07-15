<?php

declare(strict_types=1);

namespace Domain\Contracts;

interface BlameableInterface
{
    public function createdBy(): UserInterface;

    public function updatedBy(): UserInterface;
}
