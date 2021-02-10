<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserCrudController constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

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
        $plainPassword = TextField::new('plainPassword', 'Password')->setFormTypeOptions(['empty_data' => '']);
        $panel2 = FormField::addPanel('Properties');
        $roles = ArrayField::new('roles');
        $panel3 = FormField::addPanel('Set new password');
        $panel4 = FormField::addPanel('Hospitals');
        $hospital = AssociationField::new('hospital', 'Hospital');

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            $fields = [$id, $username, $email];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $fields = [$panel1, $id, $username, $email, $panel2, $roles, $panel4, $hospital];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = [$panel1, $username, $plainPassword, $email, $panel2, $roles];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = [$panel1, $id, $username, $email, $panel2, $roles, $panel3, $plainPassword, $panel4, $hospital];
        }

        return $fields;
    }
}
