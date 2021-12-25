<?php

declare(strict_types=1);

namespace App\Domain\Work\Subscriber;

use App\Domain\Work\Event\WorkEvent;
use App\Domain\Work\WorkService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkRecallSubscriber implements EventSubscriberInterface
{
    public function __construct(private WorkService $workService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkEvent::PRE_ADD => 'update',
            WorkEvent::PRE_EDIT => 'update',
        ];
    }

    public function update(WorkEvent $event): void
    {
        $this->workService->calculateNextRecallDate($event->getWork());
    }
}
