<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Planning\Entity\PlanningLog;
use App\Domain\Work\Entity\Work;
use App\Infrastructure\Notification\Enum\WorkNotificationEnum;

class UserSendNotification implements IUserSendNotification
{
    /**
     * @param iterable<IUserSendNotification> $notifiers
     */
    public function __construct(public readonly iterable $notifiers)
    {
    }

    public function notifyPlanningLogs(PlanningLog $log): bool
    {
        $result = true;
        foreach ($this->notifiers as $notifier) {
            $status = $notifier->notifyPlanningLogs($log);
            if ($status === false) {
                $result = false;
            }
        }

        return $result;
    }

    public function notifyWork(WorkNotificationEnum $type, Work $work): bool
    {
        $result = true;
        foreach ($this->notifiers as $notifier) {
            $status = $notifier->notifyWork($type, $work);
            if ($status === false) {
                $result = false;
            }
        }

        return $result;
    }
}
