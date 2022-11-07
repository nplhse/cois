<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('username'),
            EmailField::new('email')
                ->hideOnIndex(),
            TextField::new('plainPassword')
                ->setFormType(PasswordType::class)
                ->hideOnIndex()
                ->hideOnDetail(),
            ChoiceField::new('roles')
                ->setChoices(array_combine(['ROLE_USER', 'ROLE_ADMIN'], ['ROLE_USER', 'ROLE_ADMIN']))
                ->allowMultipleChoices()
                ->renderExpanded()
                ->hideOnIndex(),
            AssociationField::new('hospitals'),
            BooleanField::new('isVerified')
                ->renderAsSwitch(false),
            BooleanField::new('isParticipant')
                ->renderAsSwitch(false),
            BooleanField::new('hasCredentialsExpired')
                ->renderAsSwitch(false)
                ->hideOnIndex(),
            DateField::new('createdAt')
                ->hideOnForm(),
            DateField::new('updatedAt')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $verify = Action::new('verify')
            ->linkToRoute('admin_user_verify', function (User $user): array {
                return [
                    'id' => $user->getId(),
                ];
            })->displayIf(static function (User $user) {
                if ($user->isVerified()) {
                    return false;
                }

                return true;
            });

        $unverify = Action::new('unverify')
            ->linkToRoute('admin_user_unverify', function (User $user): array {
                return [
                    'id' => $user->getId(),
                ];
            })->displayIf(static function (User $user) {
                if ($user->isVerified()) {
                    return true;
                }

                return false;
            });

        $enable_participation = Action::new('enable_participation')
            ->linkToRoute('admin_user_enable_participation', function (User $user): array {
                return [
                    'id' => $user->getId(),
                ];
            })->displayIf(static function (User $user) {
                if ($user->isParticipant()) {
                    return false;
                }

                return true;
            });

        $disable_participation = Action::new('disable_participation')
            ->linkToRoute('admin_user_disable_participation', function (User $user): array {
                return [
                    'id' => $user->getId(),
                ];
            })->displayIf(static function (User $user) {
                if ($user->isParticipant()) {
                    return true;
                }

                return false;
            });

        $expire = Action::new('expire')
            ->linkToRoute('admin_user_expire', function (User $user): array {
                return [
                    'id' => $user->getId(),
                ];
            })->displayIf(static function (User $user) {
                if ($user->hasCredentialsExpired()) {
                    return false;
                }

                return true;
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $verify)
            ->add(Crud::PAGE_DETAIL, $unverify)
            ->add(Crud::PAGE_DETAIL, $enable_participation)
            ->add(Crud::PAGE_DETAIL, $disable_participation)
            ->add(Crud::PAGE_DETAIL, $expire);
    }
}
