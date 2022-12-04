<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Entity\Import;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => [
                Import::STATUS_SUCCESS => Import::STATUS_SUCCESS,
                Import::STATUS_PENDING => Import::STATUS_PENDING,
                Import::STATUS_INCOMPLETE => Import::STATUS_INCOMPLETE,
                Import::STATUS_FAILURE => Import::STATUS_FAILURE,
                Import::STATUS_EMPTY => Import::STATUS_EMPTY,
            ],
            'placeholder' => 'Every status',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
