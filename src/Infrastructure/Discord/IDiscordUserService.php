<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Domain\User\Entity\User;
use App\Infrastructure\Discord\Entity\DiscordRole;

interface IDiscordUserService
{
    public function getAvatarUser(User $user): string;

    /** @return DiscordRole[] */
    public function getRoles(User $user): array;
}
