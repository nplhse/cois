<?php

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('asc' === $options['default_orderBy']) {
            $builder
                ->add('orderBy', ChoiceType::class, [
                    'choices' => [
                        'Ascending' => 'asc',
                        'Descending' => 'desc',
                    ],
                ])
            ;
        } else {
            $builder
                ->add('orderBy', ChoiceType::class, [
                    'choices' => [
                        'Ascending' => 'desc',
                        'Descending' => 'asc',
                    ],
                ])
            ;
        }

        $builder->add('submit', SubmitType::class, [
            'label' => 'Sort items',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'default_orderBy' => 'asc',
            'allow_extra_fields' => true,
        ]);
    }
}
