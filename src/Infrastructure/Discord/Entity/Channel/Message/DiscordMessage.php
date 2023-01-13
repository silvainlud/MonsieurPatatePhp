<?php

declare(strict_types=1);

namespace App\Infrastructure\Discord\Entity\Channel\Message;

use App\Infrastructure\Discord\Entity\Channel\Message\Embed\DiscordEmbed;
use App\Infrastructure\Discord\Entity\DiscordUser;

class DiscordMessage
{
    private string $id;
    private string $channelId;
    private \DateTime $dateSend;

    private DiscordUser $author;

    private ?string $content = null;

    /** @var DiscordEmbed[] $embeds */
    private array $embeds = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function setChannelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    public function getDateSend(): \DateTime
    {
        return $this->dateSend;
    }

    public function setDateSend(\DateTime $dateSend): self
    {
        $this->dateSend = $dateSend;

        return $this;
    }

    public function getAuthor(): DiscordUser
    {
        return $this->author;
    }

    public function setAuthor(DiscordUser $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getEmbeds(): array
    {
        return $this->embeds;
    }

    public function setEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }

    public function addEmbed(DiscordEmbed $embed): self
    {
        if (!\in_array($embed, $this->embeds, true)) {
            $this->embeds[] = $embed;
        }

        return $this;
    }
}
