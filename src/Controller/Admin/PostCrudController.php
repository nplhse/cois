<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\CKEditorField;
use App\Domain\Enum\PostStatus;
use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            SlugField::new('slug')->hideOnIndex()->setTargetFieldName('title'),
            CKEditorField::new('content')->onlyOnForms(),
            TextField::new('content')->onlyOnDetail(),
            AssociationField::new('category')->autocomplete(),
            CollectionField::new('tags')->onlyOnDetail(),
            AssociationField::new('tags')->onlyOnForms(),
            ChoiceField::new('status')->onlyOnForms()
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', PostStatus::class)
                ->setChoices(PostStatus::cases()),
            BooleanField::new('isSticky')->hideOnIndex(),
            BooleanField::new('allowComments')->hideOnIndex(),
            DateTimeField::new('createdAt')->hideOnForm(),
            AssociationField::new('createdBy'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('updatedBy'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined()
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $view = Action::new('View Post')
            ->addCssClass('btn btn-outline-primary')
            ->linkToRoute('app_post', function (Post $post): array {
                return [
                    'slug' => $post->getSlug(),
                ];
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $view)
            ->add(Crud::PAGE_EDIT, $view)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }
}
