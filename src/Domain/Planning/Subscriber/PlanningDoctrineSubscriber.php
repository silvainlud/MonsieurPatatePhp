<?php

declare(strict_types=1);

namespace App\Domain\Planning\Subscriber;

use App\Domain\Planning\Entity\PlanningScreen;
use App\Domain\Planning\Event\PlanningScreenEvent;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PlanningDoctrineSubscriber implements EventSubscriberInterface
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof PlanningScreen) {
            $this->dispatcher->dispatch(new PlanningScreenEvent($entity), PlanningScreenEvent::PLANNING_SCREEN_UPDATE);
        }
    }
}
