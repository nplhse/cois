<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\HospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=HospitalRepository::class)
 *
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"hospital:read"}}
 * )
 */
class Hospital
{
    public const SMALL_HOSPITAL = 250;

    public const MEDIUM_HOSPITAL = 500;

    public const LARGE_HOSPITAL = 750;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"hospital:read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"hospital:read"})
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({"hospital:read"})
     */
    private ?string $address = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"hospital:read"})
     */
    private ?string $supplyArea = null;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="hospital", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"hospital:read"})
     */
    private User $owner;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"hospital:read"})
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"hospital:read"})
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"hospital:read"})
     */
    private string $size;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Groups({"hospital:read"})
     */
    private ?int $beds = null;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"hospital:read"})
     */
    private string $dispatchArea;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"hospital:read"})
     */
    private string $location;

    /**
     * @ORM\OneToMany(targetEntity=Import::class, mappedBy="hospital")
     */
    private Collection $imports;

    public function __construct()
    {
        $this->imports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSupplyArea(): ?string
    {
        return $this->supplyArea;
    }

    public function setSupplyArea(?string $supplyArea): self
    {
        $this->supplyArea = $supplyArea;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getBeds(): ?int
    {
        return $this->beds;
    }

    public function setBeds(?int $beds): self
    {
        $this->beds = $beds;

        if ($this->beds < self::SMALL_HOSPITAL) {
            $this->size = 'small';
        } elseif ($this->beds > self::SMALL_HOSPITAL && $this->beds < self::LARGE_HOSPITAL) {
            $this->size = 'medium';
        } elseif ($this->beds >= self::LARGE_HOSPITAL) {
            $this->size = 'large';
        }

        return $this;
    }

    public function getDispatchArea(): ?string
    {
        return $this->dispatchArea;
    }

    public function setDispatchArea(string $dispatchArea): self
    {
        $this->dispatchArea = $dispatchArea;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getImports(): Collection
    {
        return $this->imports;
    }

    public function addImport(Import $import): self
    {
        if (!$this->imports->contains($import)) {
            $this->imports[] = $import;
            $import->setHospital($this);
        }

        return $this;
    }

    public function removeImport(Import $import): self
    {
        if ($this->imports->removeElement($import)) {
            // set the owning side to null (unless already changed)
            if ($import->getHospital() === $this) {
                $import->setHospital(null);
            }
        }

        return $this;
    }
}
