<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Enum\Page\PageStatusEnum;
use App\Domain\Enum\Page\PageTypeEnum;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureActions(Actions $actions): Actions
    {
        $view = Action::new('view')
            ->addCssClass('btn btn-outline-primary')
            ->linkToRoute('app_page', function (Page $page): array {
                return [
                    'slug' => $page->getSlug(),
                ];
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $view)
            ->add(Crud::PAGE_EDIT, $view);
    }
}
