<?php

namespace App\Form\Filters;

use App\Domain\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class StatisticsFilterSetType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ownHospitals', OwnHospitalFilterType::class)
            ->add('startDate', DateFilterType::class)
            ->add('endDate', DateFilterType::class)
            ->add('state', StateType::class)
            ->add('dispatchArea', DispatchAreaType::class)
            ->add('supplyArea', SupplyAreaType::class)
        ;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('hospital', HospitalFilterType::class);
        } else {
            /** @var User $user */
            $user = $this->security->getUser();

            if ($user->getHospitals()->count() > 1) {
                $builder
                    ->add('hospital', HospitalFilterType::class);
            }
        }

        $builder
            ->add('reset', ResetType::class, [
                'label' => 'Reset filters',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Apply Filter',
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
