<?php

namespace App\Service\Filters;

use App\Application\Contract\FilterInterface;
use App\Domain\Entity\Hospital;
use App\Form\Filters\SizeType;
use App\Service\Filters\Traits\FilterTrait;
use App\Service\Filters\Traits\HiddenFieldTrait;
use App\Service\FilterService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SizeFilter implements FilterInterface
{
    use FilterTrait;
    use HiddenFieldTrait;

    public const Param = 'size';

    public const Sizes = [Hospital::SIZE_SMALL, Hospital::SIZE_MEDIUM, Hospital::SIZE_LARGE];

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getValue(Request $request): mixed
    {
        $size = $request->query->get('size');

        if (empty($size)) {
            $value = null;
        } else {
            $value = urldecode($size);
        }

        if (!in_array($value, self::Sizes, true)) {
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
        $form = $this->formFactory->create(SizeType::class, null, [
            'action' => $arguments['action'],
            'method' => $arguments['method'],
        ]);

        return $this->addHiddenFields($arguments['hidden'], $form);
    }

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder
    {
        $size = $this->cacheValue ?? $this->getValue($request);

        if (!isset($size)) {
            return $qb;
        }

        $qb->orWhere($arguments[FilterService::ENTITY_ALIAS].'size = :size')
                ->setParameter('size', $size)
            ;

        return $qb;
    }
}
