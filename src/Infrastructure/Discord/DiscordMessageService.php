<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord;

use App\Infrastructure\Discord\Entity\Channel\Message\DiscordMessage;
use App\Infrastructure\Discord\Entity\Channel\Message\Embed\DiscordEmbed;
use App\Infrastructure\Discord\Entity\Channel\Message\Embed\DiscordEmbedAuthor;
use App\Infrastructure\Discord\Entity\Channel\Message\EmojiIndex;
use App\Infrastructure\Discord\Entity\DiscordUser;
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
        string $authorName = IDiscordMessageService::DEFAULT_AUTHOR_NAME,
        ?string $authorUrl = IDiscordMessageService::DEFAULT_AUTHOR_URL,
        ?string $authorIconUrl = IDiscordMessageService::DEFAULT_AUTHOR_ICON_URL,
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

    public function editEmbeds(string $channelId, string $messageId, ?string $title = null, ?string $description = null, string $authorName = self::DEFAULT_AUTHOR_NAME, ?string $authorUrl = self::DEFAULT_AUTHOR_URL, ?string $authorIconUrl = self::DEFAULT_AUTHOR_ICON_URL, ?DateTime $timestamp = null, ?string $footerText = null, bool $retry = true): bool
    {
        return $this->edit($channelId, $messageId, [
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

    public function edit(string $channelId, string $messageId, array $options, bool $retry = true): bool
    {
        $resp = $this->discordClient->request(Request::METHOD_PATCH, 'channels/' . $channelId . '/messages/' . $messageId, ['json' => $options]);

        if ($resp->getStatusCode() === Response::HTTP_OK) {
            return true;
        }
        $res = json_decode($resp->getContent(false));
        if ($res->message === 'You are being rate limited.' && $retry && property_exists($res, 'retry_after')) {
            sleep((int) round((int) $res->retry_after / 1000 + 1, mode: \PHP_ROUND_HALF_UP));

            return $this->edit($channelId, $messageId, $options, false);
        }

        return false;
    }

    public function remove(string $channelId, string $messageId, bool $retry = true): bool
    {
        $resp = $this->discordClient->request(Request::METHOD_DELETE, 'channels/' . $channelId . '/messages/' . $messageId);

        if ($resp->getStatusCode() === Response::HTTP_NO_CONTENT) {
            return true;
        }
        $res = json_decode($resp->getContent(false));
        if ($res->message === 'You are being rate limited.' && $retry && property_exists($res, 'retry_after')) {
            sleep((int) round((int) $res->retry_after / 1000 + 1, mode: \PHP_ROUND_HALF_UP));

            return $this->remove($channelId, $messageId, false);
        }

        return false;
    }

    public function getLastMessage(string $channelId): ?DiscordMessage
    {
        $resp = $this->discordClient->request(Request::METHOD_GET, 'channels/' . $channelId . '/messages');
        if (
            $resp->getStatusCode() === Response::HTTP_NO_CONTENT
            || $resp->getStatusCode() === Response::HTTP_NOT_FOUND
        ) {
            return null;
        }

        $res = json_decode($resp->getContent(false));
        if (\count($res) === 0) {
            return null;
        }

        usort($res, function (\stdClass $a, \stdClass $b) {
            if ($a->timestamp === $b->timestamp) {
                return 0;
            }
            if ((int) ($a->timestamp) > (int) ($b->timestamp)) {
                return -1;
            }

            return 1;
        });

        $value = $res[array_key_last($res)];

        $msg = (new DiscordMessage())
            ->setId($value->id)
            ->setChannelId($value->channel_id)
            ->setDateSend(new DateTime($value->timestamp))
            ->setAuthor(
                (new DiscordUser((string) $value->author->id))
                    ->setUsername(EmojiIndex::replaceMessage($value->author->username))
                    ->setAvatar($value->author->avatar)
                    ->setDiscriminator($value->author->discriminator)
            );

        if (property_exists($value, 'embeds')) {
            foreach ($value->embeds as $embed) {
                $e = (new DiscordEmbed());
                if (property_exists($embed, 'title')) {
                    $e->setTitle(EmojiIndex::replaceMessage($embed->title));
                }
                if (property_exists($embed, 'description')) {
                    $e->setContent(EmojiIndex::replaceMessage($embed->description));
                }
                if (property_exists($embed, 'author')) {
                    $author = new DiscordEmbedAuthor();

                    if (property_exists($embed->author, 'name')) {
                        $author->setName(EmojiIndex::replaceMessage($embed->author->name));
                    }
                    if (property_exists($embed->author, 'icon_url')) {
                        $author->setIconUrl($embed->author->icon_url);
                    }
                    if (property_exists($embed->author, 'url')) {
                        $author->setUrl($embed->author->url);
                    }

                    $e->setAuthor($author);
                }

                $msg->addEmbed($e);
            }
        }

        return $msg;
    }
}
