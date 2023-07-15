<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\CKEditorField;
use App\Domain\Enum\PageStatus;
use App\Domain\Enum\PageType;
use App\Domain\Enum\PageVisbility;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

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
            SlugField::new('slug')->hideOnIndex()->setTargetFieldName('title'),
            CKEditorField::new('content')
                ->onlyOnForms(),
            TextField::new('content')->onlyOnDetail(),
            ChoiceField::new('type')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', PageType::class)
                ->setChoices([
                    'Type' => PageType::cases(),
                ]),
            ChoiceField::new('status')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', PageStatus::class)
                ->setChoices([
                    'Status' => PageStatus::cases(),
                ]),
            ChoiceField::new('visibility')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', PageVisbility::class)
                ->setChoices([
                    'Visibility' => PageVisbility::cases(),
                ]),
            DateField::new('createdAt')
                ->hideOnForm(),
            AssociationField::new('createdBy'),
            DateField::new('updatedAt')
                ->hideOnForm(),
            AssociationField::new('updatedBy'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['title' => 'ASC'])
            ->showEntityActionsInlined()
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
        ;
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
            ->add(Crud::PAGE_EDIT, $view)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }
}
