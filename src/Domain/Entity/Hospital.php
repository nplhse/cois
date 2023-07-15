<?php

declare(strict_types=1);

namespace Domain\Entity;

use Doctrine\Common\Collections\Collection;
use Domain\Adapter\ArrayCollection;
use Domain\Contracts\DispatchAreaInterface;
use Domain\Contracts\HospitalInterface;
use Domain\Contracts\IdentifierInterface;
use Domain\Contracts\ImportInterface;
use Domain\Contracts\StateInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Contracts\TimestampableInterface;
use Domain\Contracts\UserInterface;
use Domain\Entity\Traits\IdentifierTrait;
use Domain\Entity\Traits\TimestampableTrait;
use Domain\Enum\HospitalLocation;
use Domain\Enum\HospitalSize;
use Domain\Enum\HospitalTier;

class Hospital implements HospitalInterface, IdentifierInterface, TimestampableInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    public const BEDS_SMALL_HOSPITAL = 250;

    public const BEDS_LARGE_HOSPITAL = 750;

    public const SIZE_SMALL = 'Small';

    public const SIZE_MEDIUM = 'Medium';

    public const SIZE_LARGE = 'Large';

    public const LOCATION_RURAL = 'Rural';

    public const LOCATION_URBAN = 'Urban';

    protected string $name;

    protected UserInterface $owner;

    private ArrayCollection $associatedUsers;

    protected string $address;

    protected ?StateInterface $state = null;

    protected ?DispatchAreaInterface $dispatchArea = null;

    protected ?SupplyAreaInterface $supplyArea = null;

    protected HospitalSize $size;

    protected int $beds;

    protected HospitalLocation $location;

    protected HospitalTier $tier;

    protected Collection $imports;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
        $this->associatedUsers = new ArrayCollection();
        $this->supplyArea = null;
        $this->imports = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setOwner(UserInterface $user): self
    {
        $this->owner = $user;

        return $this;
    }

    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function addAssociatedUser(UserInterface $user): self
    {
        if (!$this->associatedUsers->contains($user)) {
            $this->associatedUsers[] = $user;
        }

        return $this;
    }

    public function removeAssociatedUser(UserInterface $user): self
    {
        $this->associatedUsers->removeElement($user);

        return $this;
    }

    public function getAssociatedUsers(): ArrayCollection
    {
        return $this->associatedUsers;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setState(StateInterface $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getState(): ?StateInterface
    {
        return $this->state;
    }

    public function setDispatchArea(DispatchAreaInterface $dispatchArea): self
    {
        $this->dispatchArea = $dispatchArea;

        return $this;
    }

    public function getDispatchArea(): ?DispatchAreaInterface
    {
        return $this->dispatchArea;
    }

    public function setSupplyArea(?SupplyAreaInterface $supplyArea): self
    {
        $this->supplyArea = $supplyArea;

        return $this;
    }

    public function getSupplyArea(): ?SupplyAreaInterface
    {
        return $this->supplyArea;
    }

    public function setSize(HospitalSize $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): HospitalSize
    {
        return $this->size;
    }

    public function setBeds(int $beds): self
    {
        if ($beds <= 0) {
            throw new \InvalidArgumentException(sprintf('Beds must be positive integer, not %d', $beds));
        }

        $this->beds = $beds;

        if ($beds <= self::BEDS_SMALL_HOSPITAL) {
            $this->size = HospitalSize::SMALL;
        } elseif ($beds < self::BEDS_LARGE_HOSPITAL) {
            $this->size = HospitalSize::MEDIUM;
        } else {
            $this->size = HospitalSize::LARGE;
        }

        return $this;
    }

    public function getBeds(): int
    {
        return $this->beds;
    }

    public function setLocation(HospitalLocation $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): HospitalLocation
    {
        return $this->location;
    }

    public function getTier(): HospitalTier
    {
        return $this->tier;
    }

    public function setTier(HospitalTier $tier): void
    {
        $this->tier = $tier;
    }

    public function getImports(): Collection
    {
        return $this->imports;
    }

    public function addImport(ImportInterface $import): self
    {
        if (!$this->imports->contains($import)) {
            $this->imports[] = $import;
            $import->setHospital($this);
        }

        return $this;
    }

    public function removeImport(ImportInterface $import): self
    {
        $this->imports->removeElement($import);

        return $this;
    }
}
