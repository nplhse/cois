<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Form\Filters\AssignmentType;
use App\Form\Filters\DateFilterType;
use App\Form\Filters\HospitalFilterType;
use App\Form\Filters\IndicationType;
use App\Form\Filters\InfectionType;
use App\Form\Filters\IsCPRFilterType;
use App\Form\Filters\IsPregnantFilterType;
use App\Form\Filters\IsShockFilterType;
use App\Form\Filters\IsVentilatedFilterType;
use App\Form\Filters\IsWithPhysicianFilterType;
use App\Form\Filters\IsWorkAccidentFilterType;
use App\Form\Filters\ModeOfTransportType;
use App\Form\Filters\OccasionType;
use App\Form\Filters\RequiresCathlabFilterType;
use App\Form\Filters\RequiresResusFilterType;
use App\Form\Filters\SecondaryDeploymentType;
use App\Form\Filters\SpecialityDetailType;
use App\Form\Filters\SpecialityType;
use App\Form\Filters\UrgencyType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ExportType extends AbstractType
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
            ->add('ownHospitals', HiddenType::class, [
                'attr' => ['class' => 'hidden-field', 'value' => 1],
            ])
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
            ->add('secondaryDeployment', SecondaryDeploymentType::class)
        ;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('hospital', HospitalFilterType::class)
            ;
        } else {
            /** @var User $user */
            $user = $this->security->getUser();

            $builder
                    ->add('hospital', HospitalFilterType::class, [
                        'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('h')
                            ->where('h.owner = :user')
                            ->setParameter('user', $user->getId())
                            ->orderBy('h.name', \Doctrine\Common\Collections\Criteria::ASC),
                    ]);
        }

        $builder
            ->add('reset', ResetType::class, [
                'label' => 'Reset',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Fetch data',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
