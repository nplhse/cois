<?php

namespace App\Service\Filters\Traits;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

trait HiddenFieldTrait
{
    private function addHiddenFields(array $hidden, FormInterface $form): FormInterface
    {
        foreach ($hidden as $key => $data) {
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
}
