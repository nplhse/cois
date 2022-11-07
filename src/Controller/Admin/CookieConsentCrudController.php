<?php

namespace App\Controller\Admin;

use App\Entity\CookieConsent;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CookieConsentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CookieConsent::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::NEW, Action::EDIT);
    }
}
