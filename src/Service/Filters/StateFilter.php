<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Form\Filters\LocationType;
use App\Repository\StateRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;

class StateFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'state';

    private FormFactoryInterface $formFactory;

    private StateRepository $stateRepository;

    public function __construct(FormFactoryInterface $formFactory, StateRepository $stateRepository)
    {
        $this->formFactory = $formFactory;
        $this->stateRepository = $stateRepository;
    }

    public function getValue(Request $request): mixed
    {
        $state = $request->query->get('state');

        if (empty($state)) {
            $value = null;
        } else {
            $value = (int) urldecode($state);
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): array
    {
        return (new FilesystemAdapter())->get('state_filter', function (ItemInterface $item) {
            $qb = $this->stateRepository->createQueryBuilder('s');
            $result = $qb->select('s.id, s.name')
                ->orderBy('s.id')
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
        return true;
    }

    public function buildForm(array $arguments): ?FormInterface
    {
        $form = $this->formFactory->create(LocationType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $state = $this->cacheValue ?? $this->getValue($request);

        if (!isset($state)) {
            return $qb;
        }

        $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'state', 'state')
            ->andWhere('state.id = :state')
                ->setParameter('state', $state)
            ;

        return $qb;
    }
}
