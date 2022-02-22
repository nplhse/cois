<?php

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AllocationFilterSetType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', StateType::class)
            ->add('supplyArea', SupplyAreaType::class)
            ->add('dispatchArea', DispatchAreaType::class)
            ->add('ownHospitals', OwnHospitalFilterType::class)
            ->add('indication', IndicationType::class)
            ->add('urgency', UrgencyType::class)
            ->add('assignment', AssignmentType::class)
            ->add('occasion', OccasionType::class)
            ->add('infection', InfectionType::class)
            ->add('modeOfTransport', ModeOfTransportType::class)
            ->add('startDate', DateFilterType::class)
            ->add('endDate', DateFilterType::class)
            ->add('requiresResus', RequiresResusFilterType::class)
            ->add('requiresCathlab', RequiresCathlabFilterType::class)
            ->add('isCPR', IsCPRFilterType::class)
            ->add('isVentilated', IsVentilatedFilterType::class)
            ->add('isShock', IsShockFilterType::class)
            ->add('isWithPhysician', IsWithPhysicianFilterType::class)
            ->add('isPregnant', IsPregnantFilterType::class)
            ->add('isWorkAccident', IsWorkAccidentFilterType::class)
            ->add('speciality', SpecialityType::class)
            ->add('specialityDetail', SpecialityDetailType::class)
        ;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('hospital', HospitalFilterType::class)
                ->add('owner', HospitalOwnerFilterType::class);
        }

        $builder
            ->add('reset', ResetType::class, [
                'label' => 'Reset filters',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filter Hospitals',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
