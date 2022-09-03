<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber\Form;

use App\Domain\NotificationSubscriber\Data\SendNotificationData;
use App\Domain\User\Entity\AbstractUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SendNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', ChoiceType::class, [
                'label' => 'Utilisateurs',
                'choices' => $options['users'],
                'choice_value' => fn (?AbstractUser $user) => $user?->getId(),
                'choice_label' => fn (?AbstractUser $user) => $user?->getUsername(),
                'translation_domain' => false,
                'attr' => ['class' => 'ts-select'],
                'row_attr' => ['class' => 'form-row'],
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'row_attr' => ['class' => 'form-row'],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'row_attr' => ['class' => 'form-row'],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('sendAll', CheckboxType::class, [
                'label' => 'Envoyer à tous les utilisateurs',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SendNotificationData::class,
            'users' => [],
            'constraints' => new Callback(
                function (SendNotificationData $object, ExecutionContextInterface $context, $payload): void {
                    if ($object->isSendAll() && $object->getUser() !== null) {
                        $context
                            ->buildViolation('Un utilisateur ne peut pas être sélectionné, si tous les utilisateurs sont destinataires.')
                            ->atPath('user')
                            ->addViolation();
                    } elseif (!$object->isSendAll() && $object->getUser() === null) {
                        $context
                            ->buildViolation('Un utilisateur doit être sélectionné, si tous les utilisateurs ne sont pas destinataires.')
                            ->atPath('user')
                            ->addViolation();
                    }
                }
            ),
        ]);
    }
}
