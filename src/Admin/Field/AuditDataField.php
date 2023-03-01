<?php

declare(strict_types=1);

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class AuditDataField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)

            // this template is used in 'index' and 'detail' pages
            ->setTemplatePath('admin/field/audit_data.html.twig')
        ;
    }
}
