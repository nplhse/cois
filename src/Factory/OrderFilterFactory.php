<?php

namespace App\Factory;

use App\Form\Filters\OrderType;
use App\Service\Filters\OrderFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

class OrderFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            OrderFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return OrderType::class;
    }

    public function setSortable(array $sortable): self
    {
        $this->defaults['sortable'] = $sortable;

        return $this;
    }

    public function getForm(): FormInterface
    {
        $form = $this->formFactory->create($this->getClass(), null, [
            'action' => $this->defaults['action'],
            'method' => $this->defaults['method'],
        ]);

        $orderChoices = [];

        foreach ($this->defaults['sortable'] as $key => $value) {
            $orderChoices[ucfirst($value)] = $value;
        }

        $form->add('sortBy', ChoiceType::class, [
            'choices' => $orderChoices,
        ]);

        if ($this->defaults['hidden']) {
            return $this->addHiddenFields($form);
        }

        return $form;
    }
}
