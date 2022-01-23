<?php

namespace App\Entity;

use App\Domain\Contracts\StateInterface;
use App\Domain\Entity\SupplyArea as DomainSupplyArea;
use App\Repository\SupplyAreaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SupplyAreaRepository::class)
 */
class SupplyArea extends DomainSupplyArea
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $name;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="supplyAreas")
     * @ORM\JoinColumn(nullable=false)
     */
    protected StateInterface $state;
}
