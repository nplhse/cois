<?php

declare(strict_types=1);

namespace App\Controller\Statistics;

use App\Service\FilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractStatisticsController extends AbstractController
{
    public const VALUE_PRECISION = 2;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected FilterService $filterService
    ) {
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
