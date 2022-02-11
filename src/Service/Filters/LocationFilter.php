<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Domain\Entity\Hospital;
use App\Form\Filters\LocationType;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class LocationFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'location';

    public const Locations = [Hospital::LOCATION_RURAL, Hospital::LOCATION_URBAN];

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getValue(Request $request): mixed
    {
        $location = $request->query->get('location');

        if (empty($location)) {
            $value = null;
        } else {
            $value = urldecode($location);
        }

        if (!in_array($value, self::Locations, true)) {
            $value = null;
        }

        return $this->setCacheValue($value);
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
        $location = $this->cacheValue ?? $this->getValue($request);

        if (!isset($location)) {
            return $qb;
        }

        $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'location = :location')
                ->setParameter('location', $location)
            ;

        return $qb;
    }
}
