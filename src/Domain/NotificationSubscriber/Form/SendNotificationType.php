<?php

namespace App\Domain\NotificationSubscriber\Form;

use App\Domain\NotificationSubscriber\Data\SendNotificationData;
use App\Domain\User\Entity\AbstractUser;
use PHP_CodeSniffer\Generators\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SendNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("user", ChoiceType::class, [
                "label" => "Utilisateurs",
                "choices" => $options["users"],
                "choice_value" => fn(?AbstractUser $user) => $user?->getId(),
                "choice_label" => fn(?AbstractUser $user) => $user?->getUsername(),
                "translation_domain" => false,
                'attr' => ['class' => 'ts-select'],
                'row_attr' => ['class' => 'form-row'],
                "required" => false,
                "constraints" => [
                    new NotNull(),
                ]
            ])
            ->add("title", TextType::class, [
                "label" => "Titre",
                'row_attr' => ['class' => 'form-row'],
                "constraints" => [
                    new NotBlank(),
                ]
            ])
            ->add("message", TextareaType::class, [
                "label" => "Message",
                'row_attr' => ['class' => 'form-row'],
                "constraints" => [
                    new NotBlank(),
                ]
            ])
            ->add("submit", SubmitType::class, [

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SendNotificationData::class,
            "users" => []
        ]);
    }
}