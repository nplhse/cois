<?php

namespace App\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Entity\Hospital as DomainHospital;
use App\Repository\HospitalRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HospitalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Hospital extends DomainHospital
{
    public const SMALL_HOSPITAL = 250;
    public const MEDIUM_HOSPITAL = 500;
    public const LARGE_HOSPITAL = 750;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\Column(type: 'text')]
    protected string $address;

    #[ORM\ManyToOne(targetEntity: State::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: true)]
    protected ?StateInterface $state = null;

    #[ORM\ManyToOne(targetEntity: DispatchArea::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: true)]
    protected ?DispatchAreaInterface $dispatchArea = null;

    #[ORM\ManyToOne(targetEntity: SupplyArea::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: true)]
    protected ?SupplyAreaInterface $supplyArea = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $owner;

    #[ORM\Column(type: 'datetime')]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $size;

    #[ORM\Column(type: 'integer')]
    protected int $beds;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $location;

    #[ORM\OneToMany(targetEntity: Import::class, mappedBy: 'hospital')]
    protected Collection $imports;
}
