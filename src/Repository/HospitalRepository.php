<?php

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function findOneByUser(User $user): ?Hospital
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.owner = :user')
            ->setParameter(':user', $user->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findById(int $id): ?Hospital
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function countHospitals(): string
    {
        $qb = $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function getHospitals(): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.name, h.id')
            ->distinct(true)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function getSupplyAreas(): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.supplyArea as element')
            ->distinct(true)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function getDispatchAreas(): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.dispatchArea as element')
            ->distinct(true)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function getHospitalPaginator(int $page, array $filter): Paginator
    {
        if (1 != $page) {
            $offset = $page * self::PAGINATOR_PER_PAGE;
        } else {
            $offset = 0;
        }

        $query = $this->createQueryBuilder('h');

        if ($filter['search']) {
            $query->where('h.name LIKE :search')
                ->setParameter('search', '%'.$filter['search'].'%')
            ;
        }

        if ($filter['location']) {
            if ('rural' == $filter['location']) {
                $query->andWhere('h.location = :location')
                    ->setParameter('location', 'rural')
                ;
            }

            if ('urban' == $filter['location']) {
                $query->andWhere('h.location = :location')
                    ->setParameter('location', 'urban')
                ;
            }
        }

        if ($filter['size']) {
            if ('small' == $filter['size']) {
                $query->andWhere('h.size = :size')
                    ->setParameter('size', 'small')
                ;
            }

            if ('medium' == $filter['size']) {
                $query->andWhere('h.size = :size')
                    ->setParameter('size', 'medium')
                ;
            }

            if ('large' == $filter['size']) {
                $query->andWhere('h.size = :size')
                    ->setParameter('size', 'large')
                ;
            }
        }

        if ($filter['supplyArea']) {
            $query->andWhere('h.supplyArea = :supplyArea')
                ->setParameter('supplyArea', $filter['supplyArea'])
            ;
        }

        if ($filter['dispatchArea']) {
            $query->andWhere('h.dispatchArea = :dispatchArea')
                ->setParameter('dispatchArea', $filter['dispatchArea'])
            ;
        }

        if (isset($filter['sortBy'])) {
            $sortBy = match ($filter['sortBy']) {
                'dispatchArea' => 'h.dispatchArea',
                'supplyArea' => 'h.supplyArea',
                'size' => 'h.beds',
                'location' => 'h.location',
                default => 'h.name',
            };
        } else {
            $sortBy = 'h.name';
        }

        if (isset($filter['orderBy'])) {
            if ('desc' === $filter['orderBy']) {
                $order = 'DESC';
            } elseif ('asc' === $filter['orderBy']) {
                $order = 'ASC';
            } else {
                $order = 'DESC';
            }
        } else {
            $order = 'DESC';
        }

        $query
            ->orderBy($sortBy, $order)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
