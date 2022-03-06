<?php

namespace App\Controller\Statistics;

use App\Service\FilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractStatisticsController extends AbstractController
{
    public const VALUE_PRECISION = 2;

    protected EntityManagerInterface $entityManager;

    protected FilterService $filterService;

    public function __construct(EntityManagerInterface $entityManager, FilterService $filterService)
    {
        $this->entityManager = $entityManager;
        $this->filterService = $filterService;
    }

    abstract public function getQueryClass(): string;

    protected function getQuery(): object
    {
        $class = $this->getQueryClass();

        return new $class($this->entityManager);
    }

    protected function getValueInPercent(int $value, int $total): float
    {
        return round(($value * 100) / $total, self::VALUE_PRECISION);
    }
}
