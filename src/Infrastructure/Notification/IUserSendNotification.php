<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Planning\Entity\PlanningLog;
use App\Domain\Work\Entity\Work;
use App\Infrastructure\Notification\Enum\WorkNotificationEnum;

interface IUserSendNotification
{
    public function notifyPlanningLogs(PlanningLog $log): bool;

    public function notifyWork(WorkNotificationEnum $type, Work $work): bool;
}
