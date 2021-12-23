<?php

declare(strict_types=1);

namespace App\Infrastructure\Parameter;

class ParameterService implements IParameterService
{
    public function __construct(private string $guildId, private string $planningWebSite)
    {
    }

    public function getGuildId(): string
    {
        return $this->guildId;
    }

    public function getPlanningWebSite(): string
    {
        return $this->planningWebSite;
    }
}
