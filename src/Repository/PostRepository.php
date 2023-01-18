<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Enum\PostStatus;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countStickyPosts(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.isSticky = :sticky')
            ->setParameter('sticky', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findStickyPosts(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.isSticky = :sticky')
            ->setParameter('sticky', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPaginator(int $page): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('p.isSticky = :sticky')
            ->setParameter('sticky', false)
            ->orderBy('p.createdAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }

    public function getArchivePaginator(int $page, int $year, ?int $month): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->andWhere('YEAR(p.createdAt) = :year')
            ->setParameter('year', $year)
            ->orderBy('p.createdAt', 'DESC')
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
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.createdAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }

    public function getTagPaginator(int $page, Tag $tag): \App\Pagination\Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PostStatus::Published)
            ->innerJoin('p.tags', 't')
            ->andWhere('t.name = :tag')
            ->setParameter('tag', $tag->getName())
            ->orderBy('p.createdAt', 'DESC')
        ;

        return (new \App\Pagination\Paginator($qb, self::PER_PAGE))->paginate($page);
    }
}
