<?php

declare(strict_types=1);

namespace App\Domain\Planning\Subscriber;

use App\Domain\Planning\Event\PlanningScreenEvent;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlanningScreenSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PlanningScreenEvent::PLANNING_SCREEN_UPDATE => 'onUpdate',
        ];
    }

    public function onUpdate(PlanningScreenEvent $e): void
    {
        $e->getScreen()->setModifiedDate(new DateTime());
    }
}
