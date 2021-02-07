<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Users')
            ->setSearchFields(['id', 'username', 'roles', 'email']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'Id');
        $panel1 = FormField::addPanel('Basics');
        $username = TextField::new('username', 'Username');
        $email = EmailField::new('email', 'Email');
        $password = TextField::new('password', 'Password')->setFormTypeOptions(['empty_data' => '']);
        $panel2 = FormField::addPanel('Properties');
        $roles = ArrayField::new('roles');

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            $fields = [$id, $username, $email];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $fields = [$panel1, $id, $username, $password, $email, $panel2, $roles];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = [$panel1, $id, $username, $password, $email, $panel2, $roles];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = [$panel1, $id, $username, $password, $email, $panel2, $roles];
        }

        return $fields;
    }
}
