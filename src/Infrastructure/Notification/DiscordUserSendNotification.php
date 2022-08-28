<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Planning\Entity\PlanningLog;
use App\Domain\Work\Entity\Work;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Discord\IDiscordMessageService;
use App\Infrastructure\Notification\Enum\WorkNotificationEnum;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;

class DiscordUserSendNotification implements IUserSendNotification
{
    public const HEADER_TITLE = ':information_source: Nom';
    public const HEADER_DATE_START = ':clock2: Début';
    public const HEADER_DATE_END = ':clock3: Fin';
    public const HEADER_TEACHER = ':teacher: Professeur';
    public const HEADER_LOCATION = ':map: Salle';

    public function __construct(
        private readonly IDiscordMessageService $messageService,
        private readonly IParameterService $parameterService,
        private readonly EntityManagerInterface $em,
        private readonly IDiscordGuildService $guildService,
        private readonly IntlExtension $intlExtension,
        private readonly Environment $twig,
    ) {
    }

    public function notifyPlanningLogs(PlanningLog $log): bool
    {
        /** @var ?GuildSettings $guildSetting */
        $guildSetting = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSetting === null) {
            return false;
        }

        $channelId = $guildSetting->getPlanningNotifyChannelId();
        if ($channelId === null) {
            return false;
        }

        $title = '';
        $fields = '';

        switch ($log->getActionType()) {
            case PlanningLog::ACTION_TYPE_ADD:
                $title .= ':new: AJOUT : ' . $log->getTitleNext();
                $fields .= '__' . self::HEADER_DATE_START . '__ : ' . $this->formateDate($log->getDateStartNext())
                    . "\n";
                $fields .= '__' . self::HEADER_DATE_END . '__ : ' . $this->formateDate($log->getDateEndNext()) . "\n";
                $fields .= '__' . self::HEADER_TEACHER . '__ : ' . $log->getTeacherNext() . "\n";
                $fields .= '__' . self::HEADER_LOCATION . '__ : ' . $log->getLocationNext() . "\n";

                break;

            case PlanningLog::ACTION_TYPE_DELETE:
                $title .= ':x: SUPPRESSION : ' . $log->getTitlePrevious();
                $fields .= '__' . self::HEADER_DATE_START . '__ : ' . $this->formateDate($log->getDateStartPrevious())
                    . "\n";
                $fields .= '__' . self::HEADER_DATE_END . '__ : ' . $this->formateDate($log->getDateEndPrevious())
                    . "\n";
                $fields .= '__' . self::HEADER_TEACHER . '__ : ' . $log->getTeacherPrevious() . "\n";
                $fields .= '__' . self::HEADER_LOCATION . '__ : ' . $log->getLocationPrevious() . "\n";

                break;

            case PlanningLog::ACTION_TYPE_UPDATE:
                $title .= ':arrows_counterclockwise: UPDATE : ' . $log->getTitlePrevious();
                if (\in_array(PlanningLog::FIELD_TITLE, $log->getUpdatedField(), true)) {
                    $fields .= '__' . self::HEADER_TITLE . '__ : ~~' . $log->getTitlePrevious() . '~~ '
                        . $log->getTitleNext() . " \n";
                }

                if (\in_array(PlanningLog::FIELD_DATE_START, $log->getUpdatedField(), true)) {
                    $fields .= '__' . self::HEADER_DATE_START . '__ : ~~'
                        . $this->formateDate($log->getDateStartPrevious()) . '~~ '
                        . $this->formateDate($log->getDateStartNext()) . " \n";
                } else {
                    $fields .= '__' . self::HEADER_DATE_START . '__ : ' . $this->formateDate($log->getDateStartNext())
                        . "\n";
                }

                if (\in_array(PlanningLog::FIELD_DATE_END, $log->getUpdatedField(), true)) {
                    $fields .= '__' . self::HEADER_DATE_END . '__ : ~~' . $this->formateDate($log->getDateEndPrevious())
                        . '~~ ' . $this->formateDate($log->getDateEndNext()) . " \n";
                } else {
                    $fields .= '__' . self::HEADER_DATE_END . '__ : ' . $this->formateDate($log->getDateEndNext())
                        . "\n";
                }

                if (\in_array(PlanningLog::FIELD_TEACHER, $log->getUpdatedField(), true)) {
                    $fields .= '__' . self::HEADER_TEACHER . '__ : ~~' . $log->getTeacherPrevious() . '~~ '
                        . $log->getTeacherNext() . " \n";
                } else {
                    $fields .= '__' . self::HEADER_TEACHER . '__ : ' . $log->getTeacherNext() . "\n";
                }

                if (\in_array(PlanningLog::FIELD_LOCATION, $log->getUpdatedField(), true)) {
                    $fields .= '__' . self::HEADER_LOCATION . '__ : ~~' . $log->getLocationPrevious() . '~~ '
                        . $log->getLocationNext() . " \n";
                } else {
                    $fields .= '__' . self::HEADER_LOCATION . '__ : ' . $log->getLocationNext() . "\n";
                }

                break;
        }

        $status = $this->messageService->sendEmbeds(
            $channelId,
            $title,
            $fields,
            'ADE - Emplois du temps',
            $this->parameterService->getPlanningWebSite(),
            $this->guildService->getCurrentGuildIcon()
        );

        return $status !== false;
    }

    public function notifyWork(WorkNotificationEnum $type, Work $work): bool
    {
        /** @var ?GuildSettings $guildSettings */
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSettings === null) {
            return false;
        }

        $channelId = $guildSettings->getWorkRecallChannelId();
        if ($channelId === null) {
            return false;
        }

        if ($type === WorkNotificationEnum::RECALL) {
            $value = $this->messageService->sendEmbeds(
                $channelId,
                ':bell: ' . $work->getWorkCategory()->getName() . ' : ' . $work->getName(),
                "[Cliquez ici](https://discord.com/channels/{$this->parameterService->getGuildId()}/{$guildSettings->getWorkAnnounceChannelId()}/{$work->getMessageId()})",
                'Rappel',
                authorUrl: $this->guildService->getCurrentGuildIcon(),
                timestamp: $work->getDueDate(),
                footerText: $work->getWorkCategory()->getName()
            );

            return $value !== false;
        }

        if ($type === WorkNotificationEnum::EDIT) {
            $msgId = $work->getMessageId();
            if ($msgId === null || !$this->messageService->isMessageExist($channelId, $msgId)) {
                return $this->notifyWork(WorkNotificationEnum::ADD, $work);
            }
            $value = $this->messageService->editEmbeds(
                $channelId,
                $msgId,
                ':new: ' . $work->getWorkCategory()->getName() . ' : ' . $work->getName(),
                $work->getDescription(),
                "Ajout d'un devoir",
                authorUrl: $this->guildService->getCurrentGuildIcon(),
                timestamp: $work->getDueDate(),
                footerText: $work->getWorkCategory()->getName()
            );

            return $value !== false;
        }

        if ($type === WorkNotificationEnum::ADD) {
            $messageId = $this->messageService->sendEmbeds(
                $channelId,
                ':new: ' . $work->getWorkCategory()->getName() . ' : ' . $work->getName(),
                $work->getDescription(),
                "Ajout d'un devoir",
                authorUrl: $this->guildService->getCurrentGuildIcon(),
                timestamp: $work->getDueDate(),
                footerText: $work->getWorkCategory()->getName()
            );

            if ($messageId !== false) {
                $work->setMessageId($messageId);
            }

            return $messageId !== false;
        }

        if ($type === WorkNotificationEnum::REMOVE) {
            $msgId = $work->getMessageId();
            if ($msgId !== null && $this->messageService->isMessageExist($channelId, $msgId)) {
                $this->messageService->remove($channelId, $channelId);
            }

            return true;
        }

        return false;
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
