<?php

namespace App\Controller\Admin;

use App\Entity\Hospital;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
            ->setSearchFields(['id', 'name', 'createdAt']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'Id');
        $name = TextField::new('name', 'Name');
        $address = TextField::new('address', 'Address');
        $supplyArea = TextField::new('supplyArea', 'Supply Area');
        $owner = AssociationField::new('owner', 'Owner');
        $createdAt = DateTimeField::new('createdAt', 'Created at');
        $updatedAt = DateTimeField::new('updatedAt', 'Updated at');

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            $fields = [$id, $name, $owner, $supplyArea, $createdAt, $updatedAt];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $fields = [$id, $name, $address, $supplyArea, $owner, $createdAt, $updatedAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = [$name, $address, $supplyArea, $owner, $createdAt, $updatedAt];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = [$name, $address, $supplyArea, $owner, $createdAt, $updatedAt];
        }

        return $fields;
    }
}
