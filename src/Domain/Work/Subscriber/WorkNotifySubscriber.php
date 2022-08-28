<?php

declare(strict_types=1);

namespace App\Domain\Work\Subscriber;

use App\Domain\Work\Event\WorkEvent;
use App\Domain\Work\IWorkProcessNotifyService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkNotifySubscriber implements EventSubscriberInterface
{
    public function __construct(private IWorkProcessNotifyService $notifyService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkEvent::PRE_ADD => 'postAdd',
            WorkEvent::POST_EDIT => 'postEdit',
            WorkEvent::POST_REMOVE => 'postRemove',
        ];
    }

    public function postAdd(WorkEvent $event): void
    {
        $this->notifyService->notifyAdd($event->getWork());
    }

    public function postEdit(WorkEvent $event): void
    {
        $this->notifyService->notifyEdit($event->getWork());
    }

    public function postRemove(WorkEvent $event): void
    {
        $this->notifyService->notifyRemove($event->getWork());
    }
}
