<?php

declare(strict_types=1);

namespace App\Domain\Entity\Traits;

use App\Domain\Contracts\UserInterface;

trait BlamableTrait
{
    protected UserInterface $createdBy;

    protected ?UserInterface $updatedBy = null;

    public function getCreatedBy(): UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?UserInterface
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?UserInterface $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
