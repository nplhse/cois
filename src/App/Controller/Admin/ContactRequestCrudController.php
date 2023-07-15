<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use Domain\Enum\ContactRequestStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class ContactRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactRequest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('email'),
            TextField::new('subject'),
            TextareaField::new('text')->hideOnIndex(),
            TextareaField::new('note')->hideOnIndex(),
            ChoiceField::new('status')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', ContactRequestStatus::class)
                ->setChoices(ContactRequestStatus::cases()),
            ChoiceField::new('status')->hideOnForm()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', ContactRequestStatus::class)
                ->setChoices([
                    ContactRequestStatus::OPEN->name => ContactRequestStatus::OPEN->value,
                    ContactRequestStatus::REJECTED->name => ContactRequestStatus::REJECTED->value,
                    ContactRequestStatus::CLOSED->name => ContactRequestStatus::CLOSED->value,
                ])
                ->renderAsBadges(),
            DateField::new('createdAt')
                ->hideOnForm(),
            DateField::new('updatedAt')
                ->hideOnForm()
                ->hideOnIndex(),
            AssociationField::new('updatedBy')
                ->hideOnIndex(),
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
            ->disable(Action::NEW);
    }
}
