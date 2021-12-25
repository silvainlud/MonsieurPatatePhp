<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use DateTime;

interface IDiscordMessageService
{
    const DEFAULT_AUTHOR_NAME = 'Monsieur Patate';
    const DEFAULT_AUTHOR_URL = 'https://silvain.eu';
    const DEFAULT_AUTHOR_ICON_URL = 'https://silvain.eu/favicon_256.png';

    public function sendEmbeds(
        string $channelId,
        ?string $title = null,
        ?string $description = null,
        string $authorName = self::DEFAULT_AUTHOR_NAME,
        ?string $authorUrl = self::DEFAULT_AUTHOR_URL,
        ?string $authorIconUrl = self::DEFAULT_AUTHOR_ICON_URL,
        ?DateTime $timestamp = null,
        ?string $footerText = null,
        bool $retry = true
    ): string|false;

    public function editEmbeds(
        string $channelId,
        string $messageId,
        ?string $title = null,
        ?string $description = null,
        string $authorName = self::DEFAULT_AUTHOR_NAME,
        ?string $authorUrl = self::DEFAULT_AUTHOR_URL,
        ?string $authorIconUrl = self::DEFAULT_AUTHOR_ICON_URL,
        ?DateTime $timestamp = null,
        ?string $footerText = null,
        bool $retry = true
    ): bool;

    public function send(string $channelId, array $options, bool $retry = true): string|false;

    public function edit(string $channelId, string $messageId, array $options, bool $retry = true): bool;

    public function remove(string $channelId, string $messageId, bool $retry = true): bool;

    public function isMessageExist(string $channelId, string $messageId): bool;
}
