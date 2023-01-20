<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Enum\CommentStatus;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextareaField::new('text')->hideOnIndex(),
            AssociationField::new('post'),
            ChoiceField::new('status')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', CommentStatus::class)
                ->setChoices(CommentStatus::cases()),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('post')
            ->add(ChoiceFilter::new('status')->setChoices([
                CommentStatus::SUBMITTED->name => CommentStatus::SUBMITTED->value,
                CommentStatus::APPROVED->name => CommentStatus::APPROVED->value,
                CommentStatus::REJECTED->name => CommentStatus::REJECTED->value,
            ]))
        ;
    }
}
