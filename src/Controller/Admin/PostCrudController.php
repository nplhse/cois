<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('slug')->hideOnIndex()->setRequired(false),
            TextEditorField::new('content')->hideOnIndex(),
            AssociationField::new('category')->autocomplete(),
            CollectionField::new('tags')->onlyOnDetail(),
            AssociationField::new('tags')->onlyOnForms(),
            DateTimeField::new('createdAt')->hideOnForm(),
            AssociationField::new('createdBy'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('updatedBy'),
        ];
    }
}
