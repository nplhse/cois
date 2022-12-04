<?php

declare(strict_types=1);

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
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    protected string $name;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    protected string $address;

    #[ORM\ManyToOne(targetEntity: State::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn]
    protected ?StateInterface $state = null;

    #[ORM\ManyToOne(targetEntity: DispatchArea::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn]
    protected ?DispatchAreaInterface $dispatchArea = null;

    #[ORM\ManyToOne(targetEntity: SupplyArea::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn]
    protected ?SupplyAreaInterface $supplyArea = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'hospitals')]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $owner;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    protected string $size;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $beds;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    protected string $location;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\Import>|\App\Entity\Import[]
     */
    #[ORM\OneToMany(targetEntity: Import::class, mappedBy: 'hospital')]
    protected Collection $imports;

    public function __construct()
    {
        parent::__construct();
        $this->imports = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
