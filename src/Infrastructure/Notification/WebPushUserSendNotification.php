<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\NotificationSubscriber\IUserPushSubscriberService;
use App\Domain\Planning\Entity\PlanningLog;
use App\Domain\Work\Entity\Work;
use App\Infrastructure\Notification\Enum\WorkNotificationEnum;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;

class WebPushUserSendNotification implements IUserSendNotification
{
    public function __construct(
        private readonly IUserPushSubscriberService $pushSubscriberService,
        private readonly IntlExtension $intlExtension,
        private readonly Environment $twig,
    ) {
    }

    public function notifyPlanningLogs(PlanningLog $log): bool
    {
        $title = match ($log->getActionType()) {
            PlanningLog::ACTION_TYPE_ADD => '🆕 AJOUT Cours : ' . $log->getTitleNext(),
            PlanningLog::ACTION_TYPE_DELETE => '❌ SUPPRESSION Cours : ' . $log->getTitlePrevious(),
            PlanningLog::ACTION_TYPE_UPDATE => '🔃 UPDATE Cours : ' . $log->getTitlePrevious(),
            default => '',
        };

        $this->pushSubscriberService->sendAll($title, 'pour le ' . $this->formateDate(
            $log->getDateStartNext() ?? $log->getDateEndNext()
                ?? $log->getDateStartPrevious() ?? $log->getDateEndPrevious()
                ?? new \DateTime()
        ));

        return true;
    }

    public function notifyWork(WorkNotificationEnum $type, Work $work): bool
    {
        if ($type === WorkNotificationEnum::RECALL) {
            $this->pushSubscriberService->sendAll('Rappel de devoir', $work->getName() . ' pour le ' .
                $this->formateDate($work->getDueDate()));
        } elseif ($type === WorkNotificationEnum::ADD) {
            $this->pushSubscriberService->sendAll("Ajout d'un devoir", $work->getName() . ' pour le ' .
                $this->formateDate($work->getDueDate()));
        } elseif ($type === WorkNotificationEnum::EDIT) {
            // Ignore
        } elseif ($type === WorkNotificationEnum::REMOVE) {
            // Ignore
        }

        return true;
    }

    private function formateDate(?\DateTime $date): string
    {
        if ($date === null) {
            return '???';
        }

        return (string) $this->intlExtension
            ->formatDateTime($this->twig, $date, pattern: "eeee d MMM HH'h'mm", locale: 'fr');
    }
}
