<?php

namespace App\Controller\Admin;

use App\Entity\Import;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Import::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('Basics');
        $id = IntegerField::new('id', 'Id');
        $user = AssociationField::new('user', 'User');
        $caption = TextField::new('caption', 'Caption');
        $contents = ChoiceField::new('contents', 'Contents')->setChoices([
            'allocation' => 'allocation',
        ]);

        $panel2 = FormField::addPanel('Properties');
        $size = IntegerField::new('size', 'Size');
        $name = TextField::new('name', 'Name');
        $path = TextField::new('path', 'Path');
        $extension = TextField::new('extension', 'Extension');
        $mimeType = TextField::new('mimeType', 'MIME Type');
        $status = TextField::new('status', 'Status');
        $isFixture = BooleanField::new('isFixture', 'Is a fixture?');

        $panel3 = FormField::addPanel('Timestamps');
        $createdAt = DateTimeField::new('createdAt', 'Created at');

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            $fields = [$id, $caption, $user, $status, $createdAt];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $fields = [$panel1, $id, $user, $caption, $contents, $panel2, $size, $name, $path, $extension, $mimeType, $status, $isFixture, $panel3, $createdAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = [$panel1, $id, $user, $caption, $contents, $panel2, $size, $name, $path, $extension, $mimeType, $status, $isFixture, $panel3, $createdAt];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = [$panel1, $id, $user, $caption, $contents, $panel2, $size, $name, $path, $extension, $mimeType, $status, $isFixture, $panel3, $createdAt];
        }

        return $fields;
    }
}
