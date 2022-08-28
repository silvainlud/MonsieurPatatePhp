<?php

declare(strict_types=1);

namespace App\Domain\Work;

use App\Domain\Work\Entity\Work;
use App\Domain\Work\Repository\WorkRepository;
use App\Infrastructure\Notification\Enum\WorkNotificationEnum;
use App\Infrastructure\Notification\IUserSendNotification;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class WorkProcessNotifyService implements IWorkProcessNotifyService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly WorkRepository $workRepository,
        private readonly IWorkService $workService,
        private readonly IUserSendNotification $sendNotification,
    ) {
    }

    public function notifyAdd(Work $work): void
    {
        $this->sendNotification->notifyWork(WorkNotificationEnum::ADD, $work);
    }

    public function notifyEdit(Work $work): void
    {
        $this->sendNotification->notifyWork(WorkNotificationEnum::EDIT, $work);
    }

    public function notifyRemove(Work $work): void
    {
        $this->sendNotification->notifyWork(WorkNotificationEnum::REMOVE, $work);
    }

    public function processRecall(): array
    {
        $now = new DateTime();
        $works = $this->workRepository->findNeedRecallWork();
        foreach ($works as $work) {
            if ($work->getRecallDate() <= $now && $work->getDueDate() >= $now) {
                $value = $this->sendNotification->notifyWork(WorkNotificationEnum::RECALL, $work);
                if ($value !== false) {
                    $this->workService->calculateNextRecallDate($work);
                }
            }
        }
        $this->em->flush();

        return $works;
    }
}
