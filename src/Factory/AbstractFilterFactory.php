<?php

namespace App\Factory;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

abstract class AbstractFilterFactory
{
    protected array $defaults;

    protected FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->defaults = $this->getDefaults();
    }

    public function getDefaults(): array
    {
        return [
            'action' => null,
            'method' => 'GET',
            'hidden' => null,
        ];
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getClass(): string
    {
        return 'NoClass';
    }

    public function setAction(string $action): self
    {
        $this->defaults['action'] = $action;

        return $this;
    }

    public function setHiddenFields(array $hiddenFields): self
    {
        $this->defaults['hidden'] = $hiddenFields;

        return $this;
    }

    protected function addHiddenFields(FormInterface $form): FormInterface
    {
        foreach ($this->defaults['hidden'] as $key => $data) {
            if (is_array($data)) {
                foreach ($data as $_key => $_data) {
                    $form->add($_key, HiddenType::class, [
                        'data' => $_data,
                    ]);
                }
            } else {
                $form->add($key, HiddenType::class, [
                    'data' => $data,
                ]);
            }
        }

        return $form;
    }

    public function getForm(): FormInterface
    {
        $form = $this->formFactory->create($this->getClass(), null, [
            'action' => $this->defaults['action'],
            'method' => $this->defaults['method'],
        ]);

        if ($this->defaults['hidden']) {
            return $this->addHiddenFields($form);
        }

        return $form;
    }
}
