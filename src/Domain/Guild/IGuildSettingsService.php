<?php

declare(strict_types=1);

namespace App\Domain\Guild;

use App\Domain\Guild\Entity\GuildSettings;

interface IGuildSettingsService
{
    public function getCurrentSettings(): GuildSettings;
}
