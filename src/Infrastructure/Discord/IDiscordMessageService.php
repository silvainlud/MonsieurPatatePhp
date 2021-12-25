<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use DateTime;

interface IDiscordMessageService
{
    public function sendEmbeds(
        string  $channelId,
        ?string $title = null,
        ?string $description = null,
        string  $authorName = 'Monsieur Patate',
        ?string $authorUrl = 'https://silvain.eu',
        ?string $authorIconUrl = 'https://silvain.eu/favicon_256.png',
        ?DateTime $timestamp = null,
        ?string $footerText = null,
        bool    $retry = true
    ): string|false;

    public function send(string $channelId, array $options, bool $retry = true): string|false;
}
