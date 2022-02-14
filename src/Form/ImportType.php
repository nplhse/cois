<?php

namespace App\Form;

use App\Entity\Hospital;
use App\Entity\Import;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ImportType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ]);

        if ($options['create']) {
            $builder
                ->add('hospital', EntityType::class, [
                    'class' => Hospital::class,
                ])
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Allocation' => 'allocation',
                    ],
                    'required' => true,
                ])
                ->add('file', DropzoneType::class, [
                    'label' => 'Import data (Must be a *.csv file!)',
                    'mapped' => true,
                    'required' => false,

                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '12M',
                            'mimeTypes' => [
                                'text/plain',
                                'text/csv',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid CSV document',
                        ]),
                    ],
                ]);

            if ($this->security->isGranted('ROLE_ADMIN')) {
                $builder->add('user', EntityType::class, [
                    'class' => User::class,
                ])
                    ->add('hospital', EntityType::class, [
                        'class' => Hospital::class,
                    ]);
            } else {
                $builder->add('hospital', EntityType::class, [
                    'class' => Hospital::class,
                    'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('h')
                        ->where('h.owner = :user')
                        ->setParameter('user', $this->security->getUser())
                        ->orderBy('h.name', 'ASC'),
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Import::class,
            'create' => true,
        ]);
    }
}
