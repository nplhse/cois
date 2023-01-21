<?php

declare(strict_types=1);

namespace App\Query;

use App\Application\Contract\ResultCollectionInterface;
use App\DataTransferObjects\ResultCollection;
use App\Domain\Enum\PostStatus;
use App\Entity\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

final class BlogArchiveQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(): ResultCollectionInterface
    {
        $result = $this->entityManager->createQueryBuilder()
            ->from(Post::class, 'p')
            ->select('COUNT(p.id) AS count, MONTH(p.createdAt) as month, YEAR(p.createdAt) as year')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->groupBy('year')
            ->addGroupBy('month')
            ->getQuery()
            ->getResult()
        ;

        return new ResultCollection($result);
    }
}
