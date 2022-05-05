<?php

namespace App\Form\Filters;

use App\Domain\Entity\User;
use App\Entity\Hospital;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class HospitalFilterType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $query_builder = fn (EntityRepository $er): \Doctrine\ORM\QueryBuilder => $er->createQueryBuilder('h')
                ->orderBy('h.name', \Doctrine\Common\Collections\Criteria::ASC);
        } else {
            /** @var User $user */
            $user = $this->security->getUser();

            $query_builder = fn (EntityRepository $er): \Doctrine\ORM\QueryBuilder => $er->createQueryBuilder('h')
                ->where('h.owner = :user')
                ->setParameter('user', $user->getId())
                ->orderBy('h.name', \Doctrine\Common\Collections\Criteria::ASC);
        }

        $resolver->setDefaults([
            'required' => false,
            'class' => Hospital::class,
            'query_builder' => $query_builder,
            'choice_label' => 'name',
            'empty_data' => '',
            'placeholder' => 'All hospitals',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
