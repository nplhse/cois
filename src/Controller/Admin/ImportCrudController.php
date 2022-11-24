<?php

namespace App\Controller\Admin;

use App\Entity\Import;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Import::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            ChoiceField::new('type')
                ->setChoices(array_combine([Import::TYPE_ALLOCATION], [Import::TYPE_ALLOCATION])),
            TextField::new('status')
                ->hideOnForm(),
            AssociationField::new('hospital'),
            AssociationField::new('user'),
            TextField::new('filePath')
                ->hideOnIndex(),
            TextField::new('fileMimeType')
                ->hideOnIndex(),
            TextField::new('fileExtension')
                ->hideOnIndex(),
            NumberField::new('fileSize')
                ->hideOnForm()
                ->hideOnIndex(),
            NumberField::new('rowCount')
                ->hideOnForm()
                ->hideOnIndex(),
            NumberField::new('runCount')
                ->hideOnForm()
                ->hideOnIndex(),
            NumberField::new('skippedRows')
                ->hideOnForm()
                ->hideOnIndex(),
            TextField::new('lastError')
                ->hideOnForm()
                ->hideOnIndex(),
            DateField::new('createdAt')
                ->hideOnForm(),
            DateField::new('updatedAt')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $refresh = Action::new('refresh')
            ->addCssClass('btn btn-outline-primary')
            ->linkToRoute('admin_import_refresh', function (Import $import): array {
                return [
                    'id' => $import->getId(),
                ];
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $refresh)
            ->add(Crud::PAGE_INDEX, $refresh);
    }
}
