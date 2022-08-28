<?php

declare(strict_types=1);

namespace App\Domain\Work\Form;

use App\Domain\Work\Entity\Work;
use App\Domain\Work\Entity\WorkCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre',
                'row_attr' => ['class' => 'form-row'],
                'required' => false]
            )
            ->add(
                'workCategory',
                EntityType::class,
                [
                    'class' => WorkCategory::class,
                    'choice_label' => 'name',
                    'label' => 'Catégorie',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->andWhere('c.active = :active')
                            ->setParameter('active', true)
                            ->orderBy('c.name', 'ASC');
                    },
                    'attr' => ['class' => 'ts-select'],
                    'required' => false,
                ]
            )
            ->add('dueDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date du devoir',
                'required' => false,
            ])->add('description', TextareaType::class, [
                'label' => 'Commentaire',
                'row_attr' => ['class' => 'form-row'],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Sauvegarder']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Work::class,
            'translation_domain' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'work_category_form';
    }
}
