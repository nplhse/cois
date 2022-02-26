<?php

namespace App\Form\Filters;

use App\Entity\Hospital;
use App\Service\Filters\HospitalFilter;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('hospital', HospitalFilterType::class);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('user', UserFilterType::class)
                ->add('hospital', HospitalFilterType::class);
        } else {
            $builder
                ->add('hospital', HospitalFilterType::class);
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
