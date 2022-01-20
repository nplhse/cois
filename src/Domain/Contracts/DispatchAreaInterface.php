<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface DispatchAreaInterface
{
    public function getId(): int;

    public function setName(string $name): self;

    public function getName(): string;

    public function setState(StateInterface $state): self;

    public function getState(): StateInterface;
}