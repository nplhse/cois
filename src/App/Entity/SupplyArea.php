<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Contracts\StateInterface;
use App\Domain\Entity\SupplyArea as DomainSupplyArea;
use App\Repository\SupplyAreaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplyAreaRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SupplyArea extends DomainSupplyArea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    protected string $name;

    #[ORM\ManyToOne(targetEntity: State::class, inversedBy: 'supplyAreas')]
    #[ORM\JoinColumn(nullable: false)]
    protected StateInterface $state;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\Hospital>|\App\Entity\Hospital[]
     */
    #[ORM\OneToMany(targetEntity: Hospital::class, mappedBy: 'supplyArea')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected \Doctrine\Common\Collections\Collection $hospitals;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        parent::__construct();
        $this->hospitals = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
