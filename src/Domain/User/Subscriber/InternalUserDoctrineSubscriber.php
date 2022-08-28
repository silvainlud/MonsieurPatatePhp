<?php

declare(strict_types=1);

namespace App\Domain\User\Subscriber;

use App\Domain\User\Entity\InternalUser;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InternalUserDoctrineSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate, Events::prePersist,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof InternalUser) {
            $this->updatePlainPassword($entity);
        }
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof InternalUser) {
            $this->updatePlainPassword($entity);
        }
    }

    private function updatePlainPassword(InternalUser $entity): void
    {
        $p = $entity->getPlainPassword();
        if ($p !== null) {
            $entity->setPassword($this->hasher->hashPassword($entity, $p));
        }
    }
}
