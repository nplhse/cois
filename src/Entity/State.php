<?php

namespace App\Entity;

use App\Domain\Adapter\ArrayCollection;
use App\Domain\Entity\State as DomainState;
use App\Repository\StateRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 */
class State extends DomainState
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
     * @ORM\OneToMany(targetEntity=DispatchArea::class, mappedBy="state")
     */
    protected \Doctrine\Common\Collections\Collection $dispatchAreas;

    public function __construct()
    {
        parent::__construct();
    }
}
