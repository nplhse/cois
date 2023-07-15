<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\AuditDataField;
use App\Admin\Field\AuditEntityField;
use App\Entity\AuditLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AuditLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AuditLog::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('user'),
            AuditEntityField::new('entityType'),
            IntegerField::new('entityId')->hideOnIndex(),
            DateTimeField::new('createdAt'),
            TextField::new('action'),
            TextField::new('requestRoute'),
            AuditDataField::new('eventData')->hideOnIndex(),
            TextField::new('ipAddress')->hideOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }
}
