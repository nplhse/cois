<?php

namespace App\Form;

use App\Domain\Enum\Page\PageStatusEnum;
use App\Domain\Enum\Page\PageTypeEnum;
use App\Entity\Page;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug', TextType::class, [
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => PageTypeEnum::getChoices(),
                'empty_data' => PageTypeEnum::Public,
            ])
            ->add('content', CKEditorType::class)
            ->add('status', ChoiceType::class, [
                'choices' => PageStatusEnum::getChoices(),
                'empty_data' => PageStatusEnum::Draft,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
