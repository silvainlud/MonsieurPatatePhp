<?php

declare(strict_types=1);

namespace App\Domain\Work\Form;

use App\Domain\Work\Entity\WorkCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de la catégorie', 'row_attr' => ['class' => 'form-row']])
            ->add('active', CheckboxType::class, ['label' => 'Activer la catégorie'])
            ->add('submit', SubmitType::class, ['label' => 'Sauvegarder']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkCategory::class,
            "translation_domain" => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'work_category_form';
    }
}
