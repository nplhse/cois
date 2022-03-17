<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Adapter\ArrayCollection;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\HospitalInterface;
use App\Domain\Contracts\IdentifierInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Contracts\TimestampableInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class Hospital implements HospitalInterface, IdentifierInterface, TimestampableInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    public const BEDS_SMALL_HOSPITAL = 250;

    public const BEDS_LARGE_HOSPITAL = 750;

    public const SIZE_SMALL = 'small';

    public const SIZE_MEDIUM = 'medium';

    public const SIZE_LARGE = 'large';

    public const LOCATION_RURAL = 'rural';

    public const LOCATION_URBAN = 'urban';

    protected string $name;

    protected UserInterface $owner;

    private ArrayCollection $associatedUsers;

    protected string $address;

    protected ?StateInterface $state = null;

    protected ?DispatchAreaInterface $dispatchArea = null;

    protected ?SupplyAreaInterface $supplyArea = null;

    private array $sizes = [self::SIZE_SMALL, self::SIZE_MEDIUM, self::SIZE_LARGE];

    protected string $size;

    protected int $beds;

    private array $locations = [self::LOCATION_RURAL, self::LOCATION_URBAN];

    protected string $location;

    protected ArrayCollection $imports;

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

    public function setSize(string $size): self
    {
        if (in_array($size, $this->sizes, true)) {
            $this->size = $size;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Size %s is not a valid option', $size));
    }

    public function getSize(): string
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
            $this->size = self::SIZE_SMALL;
        } elseif ($beds < self::BEDS_LARGE_HOSPITAL) {
            $this->size = self::SIZE_MEDIUM;
        } else {
            $this->size = self::SIZE_LARGE;
        }

        return $this;
    }

    public function getBeds(): int
    {
        return $this->beds;
    }

    public function setLocation(string $location): self
    {
        if (in_array($location, $this->locations, true)) {
            $this->location = $location;

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Location %s is not a valid option', $location));
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getImports(): ArrayCollection
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
