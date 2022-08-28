<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Domain\User\Entity\DiscordUser;
use App\Infrastructure\Discord\Entity\DiscordRole;

interface IDiscordUserService
{
    public function getAvatarUser(DiscordUser $user): string;

    /** @return DiscordRole[] */
    public function getRoles(DiscordUser $user): array;
}
