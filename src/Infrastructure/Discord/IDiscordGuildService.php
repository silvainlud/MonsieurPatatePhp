<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Infrastructure\Discord\Entity\DiscordGuild;
use App\Infrastructure\Discord\Entity\DiscordRole;

interface IDiscordGuildService
{
    public function getCurrentGuild(): DiscordGuild;

    /** @return DiscordRole[] */
    public function getRoles(): array;

    public function getCurrentGuildIcon(): string;
}
