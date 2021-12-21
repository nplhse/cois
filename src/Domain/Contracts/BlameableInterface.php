<?php

namespace App\Domain\Contracts;

interface BlameableInterface
{
    public function createdBy(): UserInterface;

    public function updatedBy(): UserInterface;
}
