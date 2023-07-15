<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Page;
use Domain\Enum\Page\PageStatusEnum;
use Domain\Enum\Page\PageTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('content', TextareaType::class)
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
