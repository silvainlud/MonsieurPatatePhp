<?php

declare(strict_types=1);

namespace App\Domain\Planning;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Planning\Entity\PlanningItem;
use App\Domain\Planning\Entity\PlanningLog;
use App\Infrastructure\Notification\IUserSendNotification;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;

class PlanningNotifyProcessService implements IPlanningNotifyProcessService
{
    public const HEADER_TITLE = ':information_source: Nom';
    public const HEADER_DATE_START = ':clock2: Début';
    public const HEADER_DATE_END = ':clock3: Fin';
    public const HEADER_TEACHER = ':teacher: Professeur';
    public const HEADER_LOCATION = ':map: Salle';

    public function __construct(
        private readonly IParameterService $parameterService,
        private readonly EntityManagerInterface $em,
        private readonly IUserSendNotification $userSendNotification,
    ) {
    }

    public function notifyLogs(): void
    {
        /** @var ?GuildSettings $guildSetting */
        $guildSetting = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSetting === null) {
            return;
        }

        $channelId = $guildSetting->getPlanningNotifyChannelId();
        if ($channelId === null) {
            return;
        }

        $logs = $this->em->getRepository(PlanningLog::class)->findAll();
        usort($logs, function (PlanningLog $a, PlanningLog $b) {
            $aDate = $a->getDateStartNext() ?? $a->getDateStartPrevious();
            $bDate = $b->getDateStartNext() ?? $b->getDateStartPrevious();

            return $aDate === $bDate ? 0 : ($aDate > $bDate ? 1 : -1);
        });

        foreach ($logs as $log) {
            if ($log->isDiscordSend()) {
                if ($log->getDateCreate() < new \DateTime('- 1 months')) {
                    $item = $this->em->getRepository(PlanningItem::class)->find($log->getPlanningUuid());
                    if ($item === null) {
                        $this->em->remove($log);
                    }
                }

                continue;
            }

            $status = $this->userSendNotification->notifyPlanningLogs($log);

            if ($status) {
                $log->setIsDiscordSend(true);
            }
        }
        $this->em->flush();
    }
}
