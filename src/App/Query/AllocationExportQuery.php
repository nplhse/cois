<?php

declare(strict_types=1);

namespace App\Query;

use App\Entity\Allocation;
use App\Service\FilterService;
use Doctrine\ORM\EntityManagerInterface;

final class AllocationExportQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function count(FilterService $filterService): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('COUNT(a.id)')
            ->from(Allocation::class, 'a');

        $arguments = [
            'joinOwner' => true,
            'alwaysOn' => true,
            FilterService::ENTITY_ALIAS => 'a.',
        ];

        $qb = $filterService->processQuery($qb, $arguments);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function execute(FilterService $filterService): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select("h.name, DATE_FORMAT(a.createdAt, '%d.%m.%Y %H:%i') as createdAt, DATE_FORMAT(a.arrivalAt, '%d.%m.%Y %H:%i') as arrivalAt, a.gender, a.age, a.urgency, a.occasion, a.assignment, a.requiresResus, a.requiresCathlab, a.isCPR, a.isVentilated, a.isShock, a.isInfectious, a.isPregnant, a.isWorkAccident, a.isWithPhysician, a.modeOfTransport, a.speciality, a.specialityDetail, a.specialityWasClosed, a.indicationCode, a.indication, a.secondaryIndicationCode, a.secondaryIndication, a.secondaryDeployment")
            ->from(Allocation::class, 'a')
            ->innerJoin('a.hospital', 'h')
        ;

        $arguments = [
            'joinOwner' => true,
            'alwaysOn' => true,
            FilterService::ENTITY_ALIAS => 'a.',
        ];

        $qb = $filterService->processQuery($qb, $arguments);

        return $qb->getQuery()->getResult();
    }
}
