<?php

declare(strict_types=1);

namespace App\Domain\Work;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Work\Entity\Work;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Discord\IDiscordMessageService;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;

class WorkDiscordNotifyService implements IWorkDiscordNotifyService
{
    public function __construct(
        private IDiscordMessageService $messageService,
        private EntityManagerInterface $em,
        private IDiscordGuildService   $guildService,
        private IParameterService      $parameterService
    )
    {
    }

    public function notifyAdd(Work $work): void
    {

        /** @var ?GuildSettings $guildSettings */
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());

        if ($guildSettings === null) {
            return;
        }

        $channelId = $guildSettings->getWorkAnnounceChannelId();

        if ($channelId === null) {
            return;
        }
        $msgId = $work->getMessageId();
        if ($msgId === null || !$this->messageService->isMessageExist($channelId, $msgId)) {
            $messageId = $this->messageService->sendEmbeds(
                $channelId,
                ':new: ' . $work->getWorkCategory()->getName() . ' : ' . $work->getName(),
                $work->getDescription(),
                "Ajout d'un devoir",
                authorUrl: $this->guildService->getCurrentGuildIcon(),
                authorIconUrl: $this->guildService->getCurrentGuildIcon(),
                timestamp: $work->getDueDate(),
                footerText: $work->getWorkCategory()->getName()
            );

            if ($messageId !== false) {
                $work->setMessageId($messageId);
            }
        }
    }

    public function processRecall(): void
    {
        // TODO: Implement processRecall() method.
    }

    public function notifyEdit(Work $work): void
    {
        /** @var ?GuildSettings $guildSettings */
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSettings === null) {
            return;
        }

        $channelId = $guildSettings->getWorkAnnounceChannelId();
        if ($channelId === null) {
            return;
        }

        $msgId = $work->getMessageId();

        if ($msgId !== null) {
            if (!$this->messageService->isMessageExist($channelId, $msgId)) {
                $this->notifyAdd($work);

                return;
            }

            $value = $this->messageService->editEmbeds(
                $channelId,
                $msgId,
                ':new: ' . $work->getWorkCategory()->getName() . ' : ' . $work->getName(),
                $work->getDescription(),
                "Ajout d'un devoir",
                authorUrl: $this->guildService->getCurrentGuildIcon(),
                authorIconUrl: $this->guildService->getCurrentGuildIcon(),
                timestamp: $work->getDueDate(),
                footerText: $work->getWorkCategory()->getName()
            );

            if ($value === false) {
                $this->notifyRemove($work);
                $this->notifyAdd($work);
            }
        } else
            $this->notifyAdd($work);
    }

    public function notifyRemove(Work $work): void
    {
        /** @var ?GuildSettings $guildSettings */
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSettings === null) {
            return;
        }

        $channelId = $guildSettings->getWorkAnnounceChannelId();
        if ($channelId === null) {
            return;
        }

        $msgId = $work->getMessageId();
        if ($msgId !== null && $this->messageService->isMessageExist($channelId, $msgId)) {
            $this->messageService->remove($channelId, $channelId);
        }
    }
}
