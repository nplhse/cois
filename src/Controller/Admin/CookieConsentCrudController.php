<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\CookieConsent;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CookieConsentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CookieConsent::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('lookupKey'),
            TextField::new('ipAddress'),
            ChoiceField::new('categories')
                ->setChoices(['Necessary' => 'necessary'])
                ->renderAsBadges(),
            DateField::new('createdAt'),
            DateField::new('updatedAt'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $revoke = Action::new('revoke', 'Revoke all Cookies', 'fa fa-trash-o')
            ->linkToRoute('admin_cookies_revoke')
            ->setCssClass('btn btn-danger')
            ->createAsGlobalAction();

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $revoke)
            ->disable(Action::NEW, Action::EDIT);
    }
}
