<?php

declare(strict_types=1);

namespace App\Domain\Guild;

use App\Domain\Guild\Entity\GuildSettings;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;

class GuildSettingsService implements IGuildSettingsService
{
    public function __construct(
        private readonly IParameterService $parameterService,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getCurrentSettings(): GuildSettings
    {
        $guild = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guild === null) {
            return new GuildSettings($this->parameterService->getGuildId());
        }

        return $guild;
    }
}
