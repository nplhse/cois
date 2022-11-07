<?php

namespace App\Controller\Admin;

use App\Domain\Enum\Page\PageStatusEnum;
use App\Domain\Enum\Page\PageTypeEnum;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('title'),
            TextField::new('slug')
                ->hideOnIndex(),
            ChoiceField::new('type')
                ->setChoices(PageTypeEnum::getChoices()),
            ChoiceField::new('status')
                ->setChoices(PageStatusEnum::getChoices()),
            TextEditorField::new('content')
                ->hideOnIndex(),
            DateField::new('createdAt')
                ->hideOnForm(),
            AssociationField::new('createdBy'),
            DateField::new('updatedAt')
                ->hideOnForm(),
            AssociationField::new('updatedBy'),
        ];
    }
}
