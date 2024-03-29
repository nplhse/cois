<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Allocation;
use App\Entity\Hospital;
use App\Entity\Import;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Contracts\AllocationInterface;
use Domain\Contracts\HospitalInterface;
use Domain\Contracts\UserInterface;
use Domain\Repository\AllocationRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * @method Allocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Allocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Allocation[]    findAll()
 * @method Allocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[AsAlias(id: AllocationRepositoryInterface::class, public: true)]
class AllocationRepository extends ServiceEntityRepository implements AllocationRepositoryInterface
{
    public const PER_PAGE = 10;

    public const DEFAULT_ORDER = 'desc';

    public const DEFAULT_SORT = 'createdAt';

    public const SORTABLE = ['createdAt', 'age', 'urgency'];

    public const SEARCHABLE = ['indication'];

    public const ENTITY_ALIAS = 'a.';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Allocation::class);
    }

    public function add(AllocationInterface $allocation): void
    {
        $this->_em->persist($allocation);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(AllocationInterface $allocation): void
    {
        $this->_em->remove($allocation);
        $this->_em->flush();
    }

    public function countAllocations(Hospital|null $entity = null): int
    {
        if ($entity instanceof HospitalInterface) {
            return $this->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->andWhere('a.hospital = :hospital')
                ->setparameter('hospital', $entity->getId())
                ->getQuery()
                ->getSingleScalarResult();
        }

        $qb = $this->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

        return $qb;
    }

    public function countAllocationsByUser(UserInterface $user): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->leftJoin('a.hospital', 'h', Expr\Join::WITH, 'h.owner = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function countAllocationsByAge(array $param = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.age, COUNT(a.age) AS counter')
            ->groupBy('a.age')
            ->orderBy('a.age', \Doctrine\Common\Collections\Criteria::ASC);

        if (isset($param['gender'])) {
            $qb->andWhere('a.gender = :gender')
                ->setParameter(':gender', $param['gender']);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAllocationsByGender(Hospital $hospital = null): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($hospital) {
            $qb->where('a.hospital = :hospital')
                ->setParameter(':hospital', $hospital->getId(), Types::INTEGER);
        }

        $qb->select('a.gender, COUNT(a.gender) AS counter')
            ->groupBy('a.gender')
            ->addOrderBy('a.gender', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function countAllocationsByTime(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.arrivalHour, COUNT(a.arrivalHour) AS counter')
            ->groupBy('a.arrivalHour')
            ->addOrderBy('a.arrivalHour', \Doctrine\Common\Collections\Criteria::DESC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByWeekday(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.arrivalWeekday, COUNT(a.arrivalWeekday) AS counter')
            ->groupBy('a.arrivalWeekday')
            ->addOrderBy('a.arrivalWeekday', \Doctrine\Common\Collections\Criteria::DESC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsByRMI(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.PZC, COUNT(a.PZC) AS counter')
            ->groupBy('a.PZC')
            ->addOrderBy('counter', \Doctrine\Common\Collections\Criteria::DESC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsBySK(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.SK, COUNT(a.SK) AS counter')
            ->groupBy('a.SK')
            ->addOrderBy('counter', \Doctrine\Common\Collections\Criteria::DESC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllPCZTexts(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.PZCText, a.PZC')
            ->addOrderBy('a.PZCText', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllAssignments(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.assignment')
            ->addOrderBy('a.assignment', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllOccasions(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.occasion')
            ->addOrderBy('a.occasion', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllTransportModes(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.modeOfTransport')
            ->addOrderBy('a.modeOfTransport', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllUrgencies(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.SK AS urgency')
            ->addOrderBy('a.SK', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllInfections(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.isInfectious AS infection')
            ->addOrderBy('a.isInfectious', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllSpecialities(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.speciality AS speciality')
            ->addOrderBy('a.speciality', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getAllSpecialityDetails(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.specialityDetail AS specialityDetail')
            ->addOrderBy('a.specialityDetail', \Doctrine\Common\Collections\Criteria::ASC)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function countAllocationsBySpeciality(bool $detail = false): array
    {
        if ($detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.specialityDetail, COUNT(a.specialityDetail) AS counter')
                ->groupBy('a.specialityDetail')
                ->orderBy('a.specialityDetail', \Doctrine\Common\Collections\Criteria::ASC);
        } else {
            $qb = $this->createQueryBuilder('a')
                ->select('a.speciality, COUNT(a.speciality) AS counter')
                ->groupBy('a.speciality')
                ->orderBy('a.speciality', \Doctrine\Common\Collections\Criteria::ASC);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAllocationsByDetail(string $detail): array
    {
        if ('requiresResus' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.requiresResus, COUNT(a.requiresResus) AS counter')
                ->groupBy('a.requiresResus')
                ->orderBy('a.requiresResus', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('requiresCathlab' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.requiresCathlab, COUNT(a.requiresCathlab) AS counter')
                ->groupBy('a.requiresCathlab')
                ->orderBy('a.requiresCathlab', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isCPR' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isCPR, COUNT(a.isCPR) AS counter')
                ->groupBy('a.isCPR')
                ->orderBy('a.isCPR', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isVentilated' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isVentilated, COUNT(a.isVentilated) AS counter')
                ->groupBy('a.isVentilated')
                ->orderBy('a.isVentilated', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isShock' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isShock, COUNT(a.isShock) AS counter')
                ->groupBy('a.isShock')
                ->orderBy('a.isShock', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isInfectious' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isInfectious, COUNT(a.isInfectious) AS counter')
                ->groupBy('a.isInfectious')
                ->orderBy('a.isInfectious', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isPregnant' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isPregnant, COUNT(a.isPregnant) AS counter')
                ->groupBy('a.isPregnant')
                ->orderBy('a.isPregnant', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isWithPhysician' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isWithPhysician, COUNT(a.isWithPhysician) AS counter')
                ->groupBy('a.isWithPhysician')
                ->orderBy('a.isWithPhysician', \Doctrine\Common\Collections\Criteria::ASC);
        } elseif ('isWorkAccident' == $detail) {
            $qb = $this->createQueryBuilder('a')
                ->select('a.isWorkAccident, COUNT(a.isWorkAccident) AS counter')
                ->groupBy('a.isWorkAccident')
                ->orderBy('a.isWorkAccident', \Doctrine\Common\Collections\Criteria::ASC);
        }

        if (!isset($qb)) {
            return [];
        }

        return $qb->getQuery()->getResult();
    }

    public function deleteByImport(Import $import = null): mixed
    {
        $qb = $this->createQueryBuilder('a')
            ->delete('App:Allocation', 'a')
            ->where('a.import = :import')
            ->setParameter(':import', $import->getId());

        return $qb->getQuery()->execute();
    }

    public function getPZCs(): array
    {
        $query = $this->createQueryBuilder('a');
        $query->select($query->expr()->substring('a.PZC', 1, 3).'AS PZC, a.PZCText')
            ->distinct(true)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getAllocationPaginator(FilterService $filterService): Paginator
    {
        $qb = $this->createQueryBuilder('a');

        $arguments = [
            'joinOwner' => true,
            PageFilter::PER_PAGE => self::PER_PAGE,
            FilterService::ENTITY_ALIAS => self::ENTITY_ALIAS,
            OrderFilter::DEFAULT_ORDER => self::DEFAULT_ORDER,
            OrderFilter::DEFAULT_SORT => self::DEFAULT_SORT,
            OrderFilter::SORTABLE => self::SORTABLE,
            SearchFilter::SEARCHABLE => self::SEARCHABLE,
        ];

        $qb = $filterService->processQuery($qb, $arguments);

        return new Paginator($qb->getQuery());
    }

    public function getIndicationsArray(): array
    {
        $indications = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.indicationCode, a.indication')
            ->orderBy('a.indicationCode', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (0 === $result['indicationCode']) {
                continue;
            }

            if (!in_array($result['indicationCode'], $indications, true)) {
                $indications[$result['indicationCode']] = $result['indicationCode'].' '.$result['indication'];
            }
        }

        return $indications;
    }

    public function getAssignmentsArray(): array
    {
        $assignments = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.assignment')
            ->addOrderBy('a.assignment', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (!in_array($result['assignment'], $assignments, true)) {
                $assignments[$result['assignment']] = $result['assignment'];
            }
        }

        return $assignments;
    }

    public function getOccasionsArray(): array
    {
        $occasions = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.occasion')
            ->addOrderBy('a.occasion', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (empty($result['occasion'])) {
                $result['occasion'] = 'No occasion';
            }

            if (!in_array($result['occasion'], $occasions, true)) {
                $occasions[$result['occasion']] = $result['occasion'];
            }
        }

        return $occasions;
    }

    public function getInfectionsArray(): array
    {
        $infections = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.isInfectious')
            ->addOrderBy('a.isInfectious', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (!in_array($result['isInfectious'], $infections, true)) {
                $infections[$result['isInfectious']] = $result['isInfectious'];
            }
        }

        return $infections;
    }

    public function getSpecialityArray(): array
    {
        $speciality = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.speciality')
            ->addOrderBy('a.speciality', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (empty($result['speciality'])) {
                continue;
            }

            if (!in_array($result['speciality'], $speciality, true)) {
                $speciality[$result['speciality']] = $result['speciality'];
            }
        }

        return $speciality;
    }

    public function getSpecialityDetailArray(): array
    {
        $speciality = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.specialityDetail')
            ->addOrderBy('a.specialityDetail', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (empty($result['specialityDetail'])) {
                continue;
            }

            if (!in_array($result['specialityDetail'], $speciality, true)) {
                $speciality[$result['specialityDetail']] = $result['specialityDetail'];
            }
        }

        return $speciality;
    }

    public function getSecondaryDeployments(): array
    {
        $secondaryDeployments = [];

        $results = $this->createQueryBuilder('a')
            ->select('a.secondaryDeployment')
            ->addOrderBy('a.secondaryDeployment', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $result) {
            if (empty($result['secondaryDeployment'])) {
                $result['secondaryDeployment'] = 'None';
            }

            if (!in_array($result['secondaryDeployment'], $secondaryDeployments, true)) {
                $secondaryDeployments[$result['secondaryDeployment']] = $result['secondaryDeployment'];
            }
        }

        return $secondaryDeployments;
    }
}
