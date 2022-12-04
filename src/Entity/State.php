<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Entity\State as DomainState;
use App\Repository\StateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateRepository::class)]
#[ORM\HasLifecycleCallbacks]
class State extends DomainState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    protected int $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    protected string $name;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\DispatchArea>|\App\Entity\DispatchArea[]
     */
    #[ORM\OneToMany(targetEntity: DispatchArea::class, mappedBy: 'state')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected \Doctrine\Common\Collections\Collection $dispatchAreas;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\SupplyArea>|\App\Entity\SupplyArea[]
     */
    #[ORM\OneToMany(targetEntity: SupplyArea::class, mappedBy: 'state')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected \Doctrine\Common\Collections\Collection $supplyAreas;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\Hospital>|\App\Entity\Hospital[]
     */
    #[ORM\OneToMany(targetEntity: Hospital::class, mappedBy: 'state')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected \Doctrine\Common\Collections\Collection $hospitals;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        parent::__construct();
        $this->dispatchAreas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->supplyAreas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->hospitals = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
