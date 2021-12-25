<?php

declare(strict_types=1);

namespace App\Domain\Work\Subscriber;

use App\Domain\Work\Entity\Work;
use App\Domain\Work\Event\WorkEvent;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkDoctrineSubscriber implements EventSubscriberInterface
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postPersist,

            Events::preUpdate,
            Events::postUpdate,

            Events::preRemove,
            Events::postRemove,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Work) {
            $this->dispatcher->dispatch(new WorkEvent($entity), WorkEvent::PRE_ADD);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Work) {
            $this->dispatcher->dispatch(new WorkEvent($entity), WorkEvent::POST_ADD);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Work) {
            $this->dispatcher->dispatch(new WorkEvent($entity), WorkEvent::PRE_EDIT);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Work) {
            $this->dispatcher->dispatch(new WorkEvent($entity), WorkEvent::POST_EDIT);
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Work) {
            $this->dispatcher->dispatch(new WorkEvent($entity), WorkEvent::PRE_REMOVE);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Work) {
            $this->dispatcher->dispatch(new WorkEvent($entity), WorkEvent::POST_REMOVE);
        }
    }
}
