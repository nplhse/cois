<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Allocation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dispatchArea')
            ->add('supplyArea')
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('creationDate')
            ->add('creationTime')
            ->add('creationDay')
            ->add('creationWeekday')
            ->add('creationMonth')
            ->add('creationYear')
            ->add('creationHour')
            ->add('creationMinute')
            ->add('arrivalAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('arrivalDate')
            ->add('arrivalTime')
            ->add('arrivalDay')
            ->add('arrivalWeekday')
            ->add('arrivalMonth')
            ->add('arrivalYear')
            ->add('arrivalHour')
            ->add('arrivalMinute')
            ->add('requiresResus')
            ->add('requiresCathlab')
            ->add('occasion')
            ->add('gender')
            ->add('age')
            ->add('isCPR')
            ->add('isVentilated')
            ->add('isShock')
            ->add('isInfectious')
            ->add('isPregnant')
            ->add('isWithPhysician')
            ->add('assignment')
            ->add('modeOfTransport')
            ->add('comment')
            ->add('speciality')
            ->add('specialityDetail')
            ->add('handoverPoint')
            ->add('specialityWasClosed')
            ->add('PZC')
            ->add('PZCText')
            ->add('secondaryPZC')
            ->add('secondaryPZCText')
            ->add('isWorkAccident')
            ->add('SK')
            ->add('RMI')
            ->add('hospital')
            ->add('import')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Allocation::class,
        ]);
    }
}
