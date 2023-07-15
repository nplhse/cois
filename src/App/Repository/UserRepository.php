<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Contracts\UserInterface;
use Domain\Repository\UserRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[AsAlias(id: UserRepositoryInterface::class, public: true)]
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    public const PER_PAGE = 10;

    public const DEFAULT_ORDER = 'desc';

    public const DEFAULT_SORT = 'id';

    public const SORTABLE = ['id', 'username', 'createdAt'];

    public const SEARCHABLE = ['username', 'email'];

    public const ENTITY_ALIAS = 'u.';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function add(UserInterface $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function delete(UserInterface $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    public function findOneById(int $id): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByUsername(string $username): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByEmail(string $email): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAdmins(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->orderBy('u.id', \Doctrine\Common\Collections\Criteria::ASC);

        return $qb->getQuery()->execute();
    }

    public function countUsers(): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function getUserPaginator(FilterService $filterService): Paginator
    {
        $qb = $this->createQueryBuilder('u');

        $arguments = [
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

    public function findHospitalOwners(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('SIZE(u.hospitals) > 0')
            ->getQuery()
            ->execute();

        return $qb;
    }

    public function findImportantUsers(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :adminRole')
            ->setParameter('adminRole', '%ROLE_ADMIN%')
            ->orWhere('u.roles LIKE :memberRole')
            ->setParameter('memberRole', '%ROLE_MEMBER%')
            ->orderBy('u.username', \Doctrine\Common\Collections\Criteria::ASC)
            ->distinct()
            ->getQuery()
            ->execute();

        return $qb;
    }
}
