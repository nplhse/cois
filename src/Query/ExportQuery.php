<?php

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Entity\Allocation;
use App\Service\FilterService;
use Doctrine\ORM\EntityManagerInterface;

final class ExportQuery
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

    public function execute(FilterService $filterService): ResultCollectionInterface
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select("a.id, hospital.name, DATE_FORMAT(a.createdAt, '%d.%m.%Y %H:%i'), DATE_FORMAT(a.arrivalAt, '%d.%m.%Y %H:%i'), a.urgency, a.occasion, a.assignment, a.requiresResus, a.requiresCathlab, a.gender, a.age, a.isCPR, a.isVentilated, a.isShock, a.isPregnant, a.isInfectious, a.isWorkAccident, a.modeOfTransport, a.speciality, a.specialityDetail, a.handoverPoint, a.indication, a.indicationCode, a.secondaryIndication, a.secondaryIndicationCode, a.secondaryDeployment")
            ->from('App:Allocation', 'a')
        ;

        $arguments = [
            'joinOwner' => true,
            'alwaysOn' => true,
            FilterService::ENTITY_ALIAS => 'a.',
        ];

        $qb = $filterService->processQuery($qb, $arguments);

        return new ResultCollection($qb->getQuery()->getResult());
    }
}
