<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Hospital;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HospitalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Hospital::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            AssociationField::new('owner'),
            AssociationField::new('state'),
            AssociationField::new('dispatchArea'),
            AssociationField::new('supplyArea')
                ->hideOnIndex(),
            ChoiceField::new('size')
                ->setChoices(array_combine([Hospital::SIZE_LARGE, Hospital::SIZE_MEDIUM, Hospital::SIZE_SMALL], [Hospital::SIZE_LARGE, Hospital::SIZE_MEDIUM, Hospital::SIZE_SMALL]))
                ->hideOnIndex(),
            NumberField::new('beds')
                ->hideOnIndex(),
            ChoiceField::new('location')
                ->setChoices(array_combine([Hospital::LOCATION_RURAL, Hospital::LOCATION_URBAN], [Hospital::LOCATION_RURAL, Hospital::LOCATION_URBAN]))
                ->hideOnIndex(),
            DateField::new('createdAt')
                ->hideOnForm(),
            DateField::new('updatedAt')
                ->hideOnForm(),
        ];
    }
}
