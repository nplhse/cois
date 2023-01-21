<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            SlugField::new('slug')->hideOnIndex()->setTargetFieldName('name'),
            DateTimeField::new('createdAt')->hideOnForm(),
            AssociationField::new('createdBy'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('updatedBy'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['name' => 'ASC'])
            ->showEntityActionsInlined()
        ;
    }
}
