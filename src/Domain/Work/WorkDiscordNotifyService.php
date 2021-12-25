<?php

namespace App\Domain\Work;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Work\Entity\Work;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Discord\IDiscordMessageService;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;

class WorkDiscordNotifyService implements IWorkDiscordNotifyService
{

    public function __construct(private IDiscordMessageService $messageService,
                                private EntityManagerInterface $em,
                                private IDiscordGuildService   $guildService,
                                private IParameterService      $parameterService)
    {
    }

    public function notifyAdd(Work $work): void
    {
        /** @var ?GuildSettings $guildSettings */
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($this->guildService === null)
            return;

        $channelId = $guildSettings->getWorkAnnounceChannelId();
        if ($channelId === null)
            return;

        if ($work->getMessageId() === null) {

            $messageId = $this->messageService->sendEmbeds($channelId,
                ":new: " . $work->getWorkCategory()->getName() . " : " . $work->getName(),
                $work->getDescription(), "Ajout d'un devoir" ,
                authorUrl: $this->guildService->getCurrentGuildIcon(),
                authorIconUrl: $this->guildService->getCurrentGuildIcon(),
                timestamp: $work->getDueDate(), footerText: $work->getWorkCategory()->getName());

            if ($messageId !== false) {
                $work->setMessageId($messageId);
                $this->em->flush();
            }
        }
    }

    public function processRecall(): void
    {
        // TODO: Implement processRecall() method.
    }

    public function notifyEdit(Work $work): void
    {
        // TODO: Implement notifyEdit() method.
    }

    public function notifyModify(Work $work): void
    {
        // TODO: Implement notifyModify() method.
    }
}