<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Form\Filters\LocationType;
use App\Repository\SupplyAreaRepository;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;

class SupplyAreaFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'supplyArea';

    private FormFactoryInterface $formFactory;

    private SupplyAreaRepository $supplyAreaRepository;

    public function __construct(FormFactoryInterface $formFactory, SupplyAreaRepository $supplyAreaRepository)
    {
        $this->formFactory = $formFactory;
        $this->supplyAreaRepository = $supplyAreaRepository;
    }

    public function getValue(Request $request): mixed
    {
        $area = $request->query->get('supplyArea');

        if (empty($area)) {
            $value = null;
        } else {
            $value = (int) urldecode($area);
        }

        return $this->setCacheValue($value);
    }

    public function getAltValues(): array
    {
        return (new FilesystemAdapter())->get('supply_area_filter', function (ItemInterface $item) {
            $qb = $this->supplyAreaRepository->createQueryBuilder('s');
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
        $area = $this->cacheValue ?? $this->getValue($request);

        if (!isset($area)) {
            return $qb;
        }

        $qb->leftJoin($arguments[FilterService::ENTITY_ALIAS].'supplyArea', 'supplyArea')
            ->andWhere('supplyArea.id = :supplyArea')
                ->setParameter('supplyArea', $area)
            ;

        return $qb;
    }
}
