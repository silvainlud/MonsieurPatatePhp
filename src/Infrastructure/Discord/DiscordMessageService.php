<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordMessageService implements IDiscordMessageService
{
    public function __construct(
        private HttpClientInterface $discordClient,
    ) {
    }

    public function sendEmbeds(
        string $channelId,
        ?string $title = null,
        ?string $description = null,
        string $authorName = 'Monsieur Patate',
        ?string $authorUrl = 'https://silvain.eu',
        ?string $authorIconUrl = 'https://silvain.eu/favicon_256.png',
        ?DateTime $timestamp = null,
        ?string $footerText = null,
        bool $retry = true
    ): string|false {
        return $this->send($channelId, [
            'embeds' => [
                [
                    'title' => $title,
                    'description' => $description,
                    'timestamp' => $timestamp?->format(DateTimeInterface::W3C),
                    'footer' => [
                        'text' => $footerText,
                    ],
                    'author' => [
                        'name' => $authorName,
                        'url' => $authorUrl,
                        'icon_url' => $authorIconUrl,
                    ],
                ],
            ],
        ], $retry);
    }

    public function send(string $channelId, array $options, bool $retry = true): string|false
    {
        $resp = $this->discordClient->request(Request::METHOD_POST, 'channels/' . $channelId . '/messages', ['json' => $options]);
        $res = json_decode($resp->getContent(false));
        if ($resp->getStatusCode() === Response::HTTP_OK) {
            return $res->id;
        }
        if ($res->message === 'You are being rate limited.' && $retry && property_exists($res, 'retry_after')) {
            sleep((int) round((int) $res->retry_after / 1000 + 1, mode: \PHP_ROUND_HALF_UP));

            return $this->send($channelId, $options, false);
        }

        return false;
    }

    public function isMessageExist(string $channelId, string $messageId): bool
    {
        $resp = $this->discordClient->request(Request::METHOD_GET, 'channels/' . $channelId . '/messages/' . $messageId);

        return $resp->getStatusCode() === Response::HTTP_OK;
    }
}
