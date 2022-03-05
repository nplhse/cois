<?php

namespace App\Form\Filters;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ImportFilterSetType extends AbstractType
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
            ->add('ownImports', OwnImportFilterType::class)
            ->add('ownHospitals', OwnHospitalFilterType::class)
            ->add('status', ImportStatusType::class);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('user', UserFilterType::class)
                ->add('hospital', HospitalFilterType::class);
        } else {
            /** @var User $user */
            $user = $this->security->getUser();

            if ($user->getHospitals()->count() > 1) {
                $builder
                    ->add('hospital', HospitalFilterType::class, [
                        'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('h')
                            ->where('h.owner = :user')
                            ->setParameter('user', $user->getId())
                            ->orderBy('h.name', 'ASC'),
                    ]);
            }
        }

        $builder
            ->add('reset', ResetType::class, [
                'label' => 'Reset filters',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filter Imports',
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
