<?php

namespace App\Domain\Contracts;

interface BlameableInterface
{
    public function createdBy();

    public function updatedBy();
}
