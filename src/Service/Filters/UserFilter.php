<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Repository\UserRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;

class UserFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'user';

    private Security $security;

    private UserRepository $userRepository;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function getValue(Request $request): mixed
    {
        $user = (int) $request->query->get('user');

        if (empty($user)) {
            $value = null;
        }

        if ($user > 0) {
            $value = $user;
        } else {
            $value = null;
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): array
    {
        return (new FilesystemAdapter())->get('user_filter', function (ItemInterface $item) {
            $qb = $this->userRepository->createQueryBuilder('u');
            $result = $qb->select('u.id, u.username as name')
                ->orderBy('u.id')
                ->getQuery()
                ->getArrayResult();

            $values = [];

            foreach ($result as $row) {
                $values[$row['id']] = $row['name'];
            }

            return $values;
        });
    }

    public function supportsForm(): bool
    {
        return false;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        return null;
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $user = $this->cacheValue ?? $this->getValue($request);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if (!isset($user)) {
                return $qb;
            }

            return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'user = :user')
                ->setParameter('user', $user)
                ;
        }

        if (!isset($user)) {
            return $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'user = :user')
                ->setParameter('user', $this->security->getUser())
                ;
        }

        return $qb->andWhere($arguments[FilterService::ENTITY_ALIAS].'user = :user')
            ->setParameter('user', $this->security->getUser())
            ;
    }
}
