<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Infrastructure\Discord\Entity\Channel\AbstractDiscordChannel;
use App\Infrastructure\Discord\Entity\DiscordGuild;
use App\Infrastructure\Discord\Entity\DiscordMember;
use App\Infrastructure\Discord\Entity\DiscordRole;

interface IDiscordGuildService
{
    public function getCurrentGuild(): DiscordGuild;

    /** @return DiscordRole[] */
    public function getRoles(): array;

    public function getCurrentGuildIcon(): string;

    /** @return AbstractDiscordChannel[] */
    public function getChannels(int $guildId): array;

    /** @return DiscordMember[] */
    public function getGuildMembers(string $guildId): array;

    public function getGuildMember(string $guildId, string $memberId): ?DiscordMember;

    public function isGuildMember(string $guildId, string $memberId): bool;
}
