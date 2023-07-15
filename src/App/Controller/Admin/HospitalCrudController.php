<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Hospital;
use Domain\Enum\HospitalLocation;
use Domain\Enum\HospitalSize;
use Domain\Enum\HospitalTier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

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
            TextareaField::new('address')->hideOnIndex(),
            AssociationField::new('owner'),
            AssociationField::new('state'),
            AssociationField::new('dispatchArea'),
            AssociationField::new('supplyArea')
                ->hideOnIndex(),
            ChoiceField::new('size')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', HospitalSize::class)
                ->setChoices(HospitalSize::cases()),
            NumberField::new('beds')
                ->hideOnIndex(),
            ChoiceField::new('location')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', HospitalLocation::class)
                ->setChoices(HospitalLocation::cases()),
            ChoiceField::new('tier')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', HospitalTier::class)
                ->setChoices(HospitalTier::cases()),
            DateField::new('createdAt')
                ->hideOnForm(),
            DateField::new('updatedAt')
                ->hideOnForm(),
        ];
    }
}
