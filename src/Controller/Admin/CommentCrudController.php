<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Enum\CommentStatus;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
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
            TextField::new('username'),
            EmailField::new('email'),
            AssociationField::new('user'),
            TextareaField::new('text')->hideOnIndex(),
            AssociationField::new('post'),
            ChoiceField::new('status')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', CommentStatus::class)
                ->setChoices(CommentStatus::cases()),
            ChoiceField::new('status')->hideOnForm()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', CommentStatus::class)
                ->setChoices([
                    CommentStatus::SUBMITTED->name => CommentStatus::SUBMITTED->value,
                    CommentStatus::REJECTED->name => CommentStatus::REJECTED->value,
                    CommentStatus::APPROVED->name => CommentStatus::APPROVED->value,
                ])
                ->renderAsBadges(),
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC', 'status' => 'ASC'])
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approve')
            ->addCssClass('btn btn-outline-success')
            ->linkToRoute('admin_comment_approve', function (Comment $comment): array {
                return [
                    'id' => $comment->getId(),
                ];
            });

        $reject = Action::new('reject')
            ->addCssClass('btn btn-outline-danger')
            ->linkToRoute('admin_comment_reject', function (Comment $comment): array {
                return [
                    'id' => $comment->getId(),
                ];
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $approve)
            ->add(Crud::PAGE_DETAIL, $reject)
            ->disable(Action::NEW);
    }
}
