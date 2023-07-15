<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Import;
use App\Entity\SkippedRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SkippedRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkippedRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkippedRow[]    findAll()
 * @method SkippedRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkippedRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkippedRow::class);
    }

    public function deleteByImport(Import $import): mixed
    {
        $qb = $this->createQueryBuilder('s')
            ->delete('App:SkippedRow', 's')
            ->where('s.import = :import')
            ->setParameter(':import', $import->getId());

        return $qb->getQuery()->execute();
    }
}
