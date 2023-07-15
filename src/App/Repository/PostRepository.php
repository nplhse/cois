<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Enum\PostStatus;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public const PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findRecentPosts(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findStickyPosts(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->andWhere('p.isSticky = :sticky')
            ->setParameter('sticky', true)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPaginator(int $page): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->andWhere('p.isSticky = :sticky')
            ->setParameter('sticky', false)
            ->orderBy('p.publishedAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }

    public function getFeedPaginator(int $page): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->orderBy('p.publishedAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }

    public function getArchivePaginator(int $page, int $year, ?int $month): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->andWhere('YEAR(p.createdAt) = :year')
            ->setParameter('year', $year)
            ->orderBy('p.publishedAt', 'DESC')
        ;

        if ($month) {
            $qb->andWhere('MONTH(p.createdAt) = :month')
                ->setParameter('month', $month);
        }

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }

    public function getCategoryPaginator(int $page, Category $category): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->andWhere('p.category = :category')
            ->setParameter('category', $category->getId())
            ->orderBy('p.publishedAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }

    public function getTagPaginator(int $page, Tag $tag): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.publishedAt <= :date')
            ->setParameter('date', new \DateTimeImmutable(), Types::DATE_IMMUTABLE)
            ->innerJoin('p.tags', 't')
            ->andWhere('t.name = :tag')
            ->setParameter('tag', $tag->getName())
            ->orderBy('p.publishedAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }
}
