<?php

namespace App\Entity;

use App\Domain\Contracts\StateInterface;
use App\Domain\Entity\DispatchArea as DomainDispatchArea;
use App\Repository\DispatchAreaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DispatchAreaRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class DispatchArea extends DomainDispatchArea
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
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="dispatchAreas")
     * @ORM\JoinColumn(nullable=false)
     */
    protected StateInterface $state;

    /**
     * @ORM\OneToMany(targetEntity=Hospital::class, mappedBy="dispatchArea")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected \Doctrine\Common\Collections\Collection $hospitals;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $updatedAt = null;
}
