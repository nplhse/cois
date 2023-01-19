<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Enum\CommentStatus;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            TextEditorField::new('text')->hideOnIndex(),
            AssociationField::new('post'),
            ChoiceField::new('status')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', CommentStatus::class)
                ->setChoices(CommentStatus::cases()),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
