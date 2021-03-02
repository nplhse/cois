<?php

namespace App\Controller\Admin;

use App\Entity\Hospital;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HospitalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Hospital::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Hospitals')
            ->setSearchFields(['id', 'name', 'createdAt', 'supplyArea', 'dispatchArea']);
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('Basics');
        $id = IntegerField::new('id', 'Id');
        $name = TextField::new('name', 'Name');
        $address = TextareaField::new('address', 'Address');
        $supplyArea = TextField::new('supplyArea', 'Supply Area');
        $dispatchArea = TextField::new('dispatchArea', 'Dispatch Area');

        $panel2 = FormField::addPanel('Properties');
        $owner = AssociationField::new('owner', 'Owner');
        $location = ChoiceField::new('location', 'Location')->setChoices([
            'urban' => 'urban',
            'rural' => 'rural',
        ]);
        $beds = IntegerField::new('beds', 'Beds');
        $size = ChoiceField::new('size', 'Size')->setChoices([
            'large' => Hospital::LARGE_HOSPITAL,
            'medium' => Hospital::MEDIUM_HOSPITAL,
            'small' => Hospital::SMALL_HOSPITAL,
        ]);

        $panel3 = FormField::addPanel('Timestamps');
        $createdAt = DateTimeField::new('createdAt', 'Created at');
        $updatedAt = DateTimeField::new('updatedAt', 'Updated at');

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            $fields = [$id, $name, $owner, $supplyArea, $dispatchArea, $size, $location];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $fields = [$panel1, $id, $name, $address, $supplyArea, $dispatchArea, $panel2, $owner, $beds, $size, $location, $panel3, $createdAt, $updatedAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = [$panel1, $name, $address, $supplyArea, $dispatchArea, $panel2, $owner, $beds, $size, $location, $panel3, $createdAt, $updatedAt];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = [$panel1, $name, $address, $supplyArea, $dispatchArea, $panel2, $owner, $beds, $size, $location, $panel3, $createdAt, $updatedAt];
        }

        return $fields;
    }
}
